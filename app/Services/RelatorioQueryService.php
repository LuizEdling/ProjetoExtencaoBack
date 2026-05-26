<?php

namespace App\Services;

use App\Models\Adocao;
use App\Models\Animal;
use App\Models\AnimalState;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelatorioQueryService
{
    public const STATE_ESPERANDO_CONSULTA = 'Esperando consulta';

    public const STATE_CONSULTADO = 'Consultado';

    public const STATE_EM_CIRURGIA = 'Em cirurgia';

    public const STATE_ADOTADO = 'Adotado';

    /**
     * @param array{
     *     cadastro_de: string,
     *     cadastro_ate: string,
     *     serie_de: string,
     *     serie_ate: string,
     *     apenas_mes_atual: bool
     * } $f
     *
     * @return array{
     *     cadastros_por_mes: list<array{ano_mes: string, total: int}>,
     *     estados_clinica: array{
     *         esperando_consulta: int,
     *         consultado: int,
     *         em_cirurgia: int
     *     },
     *     abrigados_adotados_por_mes: list<array{ano_mes: string, abrigados: int, adotados: int}>
     * }
     */
    public function buildDashboard(array $f): array
    {
        $cadastroInicio = Carbon::createFromFormat('Y-m', $f['cadastro_de'])->startOfMonth();
        $cadastroFim = Carbon::createFromFormat('Y-m', $f['cadastro_ate'])->endOfMonth();

        $serieInicio = Carbon::createFromFormat('Y-m', $f['serie_de'])->startOfMonth();
        $serieFim = Carbon::createFromFormat('Y-m', $f['serie_ate'])->endOfMonth();

        return [
            'cadastros_por_mes' => $this->cadastrosPorMes($cadastroInicio, $cadastroFim),
            'estados_clinica' => $this->estadosClinicaSnapshot(),
            'abrigados_adotados_por_mes' => $this->abrigadosAdotadosPorMes($serieInicio, $serieFim),
        ];
    }

    /**
     * @return list<array{ano_mes: string, total: int}>
     */
    private function cadastrosPorMes(Carbon $inicio, Carbon $fim): array
    {
        $meses = $this->expandirMeses($inicio->copy()->startOfMonth(), $fim->copy()->startOfMonth());
        $col = $this->monthSqlExpression('animals.data_ficha');

        $totais = Animal::query()
            ->whereBetween('data_ficha', [$inicio->copy()->startOfDay(), $fim->copy()->endOfDay()])
            ->selectRaw("{$col} as ano_mes, COUNT(*) as total")
            ->groupBy('ano_mes')
            ->orderBy('ano_mes')
            ->pluck('total', 'ano_mes')
            ->map(fn ($v) => (int) $v)
            ->all();

        $lista = [];
        foreach ($meses as $ym) {
            $lista[] = [
                'ano_mes' => $ym,
                'total' => (int) ($totais[$ym] ?? 0),
            ];
        }

        return $lista;
    }

    /**
     * @return array{esperando_consulta: int, consultado: int, em_cirurgia: int}
     */
    private function estadosClinicaSnapshot(): array
    {
        $nomes = [
            self::STATE_ESPERANDO_CONSULTA,
            self::STATE_CONSULTADO,
            self::STATE_EM_CIRURGIA,
        ];

        $counts = Animal::query()
            ->join('animal_states', 'animal_states.id', '=', 'animals.animal_state_id')
            ->whereIn('animal_states.nome', $nomes)
            ->selectRaw('animal_states.nome as nome, COUNT(*) as c')
            ->groupBy('animal_states.nome')
            ->pluck('c', 'nome')
            ->map(fn ($v) => (int) $v)
            ->all();

        return [
            'esperando_consulta' => (int) ($counts[self::STATE_ESPERANDO_CONSULTA] ?? 0),
            'consultado' => (int) ($counts[self::STATE_CONSULTADO] ?? 0),
            'em_cirurgia' => (int) ($counts[self::STATE_EM_CIRURGIA] ?? 0),
        ];
    }

    /**
     * Adotados: adoções com data_adocao naquele mês.
     * Abrigados: animais com data_ficha naquele mês que não estão no estado Adotado (situação atual).
     *
     * @return list<array{ano_mes: string, abrigados: int, adotados: int}>
     */
    private function abrigadosAdotadosPorMes(Carbon $serieInicio, Carbon $serieFim): array
    {
        $meses = $this->expandirMeses($serieInicio->copy()->startOfMonth(), $serieFim->copy()->startOfMonth());

        $adotadoId = AnimalState::query()->where('nome', self::STATE_ADOTADO)->value('id');

        $colFicha = $this->monthSqlExpression('animals.data_ficha');
        $colAdocao = $this->monthSqlExpression('adocao.data_adocao');

        $adotadosPorMes = Adocao::query()
            ->whereBetween('data_adocao', [$serieInicio->copy()->startOfDay(), $serieFim->copy()->endOfDay()])
            ->selectRaw("{$colAdocao} as ano_mes, COUNT(*) as total")
            ->groupBy('ano_mes')
            ->orderBy('ano_mes')
            ->pluck('total', 'ano_mes')
            ->map(fn ($v) => (int) $v)
            ->all();

        $abrigadosQuery = Animal::query()
            ->whereBetween('data_ficha', [$serieInicio->copy()->startOfDay(), $serieFim->copy()->endOfDay()]);

        if ($adotadoId !== null) {
            $abrigadosQuery->where('animal_state_id', '!=', (int) $adotadoId);
        }

        $abrigadosPorMes = $abrigadosQuery
            ->selectRaw("{$colFicha} as ano_mes, COUNT(*) as total")
            ->groupBy('ano_mes')
            ->orderBy('ano_mes')
            ->pluck('total', 'ano_mes')
            ->map(fn ($v) => (int) $v)
            ->all();

        $lista = [];
        foreach ($meses as $ym) {
            $lista[] = [
                'ano_mes' => $ym,
                'abrigados' => (int) ($abrigadosPorMes[$ym] ?? 0),
                'adotados' => (int) ($adotadosPorMes[$ym] ?? 0),
            ];
        }

        return $lista;
    }

    /**
     * @return list<string>
     */
    private function expandirMeses(Carbon $inicioMes, Carbon $fimMes): array
    {
        $out = [];
        $cursor = $inicioMes->copy()->startOfMonth();
        $fim = $fimMes->copy()->startOfMonth();
        while ($cursor <= $fim) {
            $out[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        return $out;
    }

    private function monthSqlExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char(({$column})::timestamp, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
