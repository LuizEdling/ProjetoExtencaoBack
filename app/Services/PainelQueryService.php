<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\AnimalState;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PainelQueryService
{
    public const STATE_ESPERANDO_CONSULTA = 'Esperando consulta';

    public const STATE_EM_CIRURGIA = 'Em cirurgia';

    public const STATE_ADOTADO = 'Adotado';

    /**
     * @return array{
     *     resumos: list<array{
     *         id: string,
     *         titulo: string,
     *         valor: string,
     *         legenda: string,
     *         legenda_variant: string,
     *         icon: string
     *     }>,
     *     fila_atendimento: list<array{
     *         id: string,
     *         nome: string,
     *         especie: string,
     *         estado_nome: string,
     *         estado_alterado_em: string|null,
     *         data_entrada: string
     *     }>,
     *     cadastros_mes: list<array{
     *         id: string,
     *         nome: string,
     *         tipo_raca: string,
     *         data: string
     *     }>
     * }
     */
    public function build(): array
    {
        $now = Carbon::now();

        return [
            'resumos' => $this->buildResumos($now),
            'fila_atendimento' => $this->buildFilaAtendimento(),
            'cadastros_mes' => $this->buildCadastrosMes($now),
        ];
    }

    /**
     * @return list<array{id: string, titulo: string, valor: string, legenda: string, legenda_variant: string, icon: string}>
     */
    private function buildResumos(Carbon $now): array
    {
        $inicioMes = $now->copy()->startOfMonth();
        $fimMes = $now->copy()->endOfMonth();
        $inicioMesAnterior = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $fimMesAnterior = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $cadastradosMes = $this->countCadastrosInRange($inicioMes, $fimMes);
        $cadastradosMesAnterior = $this->countCadastrosInRange($inicioMesAnterior, $fimMesAnterior);
        if ($cadastradosMesAnterior > 0) {
            $delta = round((($cadastradosMes - $cadastradosMesAnterior) / $cadastradosMesAnterior) * 100);
            $legendaCadastrados = sprintf('%+d%% vs mês passado', $delta);
        } else {
            $legendaCadastrados = $cadastradosMes > 0 ? 'Primeiros cadastros no período' : 'Nenhum cadastro esse mês';
        }

        $aguardando = $this->countAnimalsInState(self::STATE_ESPERANDO_CONSULTA);
        $emCirurgia = $this->countAnimalsInState(self::STATE_EM_CIRURGIA);
        $legendaAguardando = $emCirurgia > 0
            ? sprintf('%d em cirurgia', $emCirurgia)
            : 'Nenhum em cirurgia no momento';

        $adocoesMes = $this->countAdoptionsInRange($inicioMes, $fimMes);
        $adocoesMesAnterior = $this->countAdoptionsInRange($inicioMesAnterior, $fimMesAnterior);
        if ($adocoesMesAnterior > 0) {
            $delta = round((($adocoesMes - $adocoesMesAnterior) / $adocoesMesAnterior) * 100);
            $legendaAdocoes = sprintf('%+d%% vs mês passado', $delta);
        } else {
            $legendaAdocoes = $adocoesMes > 0 ? 'Primeiras adoções no período' : 'Nenhuma adoção este mês';
        }

        $abrigoTotal = Animal::query()
            ->whereHas('animalState', fn (Builder $q) => $q->where('nome', '!=', self::STATE_ADOTADO))
            ->count();

        $porEspecie = Animal::query()
            ->whereHas('animalState', fn (Builder $q) => $q->where('nome', '!=', self::STATE_ADOTADO))
            ->selectRaw('especie, COUNT(*) as total')
            ->groupBy('especie')
            ->orderByDesc('total')
            ->get();

        $partes = $porEspecie->map(fn ($row) => sprintf('%d %s', $row->total, $row->especie))->all();
        $legendaAbrigo = count($partes) > 0 ? implode(', ', $partes) : 'Nenhum animal abrigado';

        return [
            [
                'id' => 'cadastrados_mes',
                'titulo' => 'Cadastrados esse Mês',
                'valor' => (string) $cadastradosMes,
                'legenda' => $legendaCadastrados,
                'legenda_variant' => $cadastradosMes > 0 ? 'success' : 'neutral',
                'icon' => 'paw',
            ],
            [
                'id' => 'aguardando_atendimento',
                'titulo' => 'Aguardando Atendimento',
                'valor' => (string) $aguardando,
                'legenda' => $legendaAguardando,
                'legenda_variant' => $aguardando > 0 ? 'warning' : 'neutral',
                'icon' => 'dog',
            ],
            [
                'id' => 'adocoes_mes',
                'titulo' => 'Adoções do Mês',
                'valor' => (string) $adocoesMes,
                'legenda' => $legendaAdocoes,
                'legenda_variant' => $adocoesMes > 0 ? 'success' : 'neutral',
                'icon' => 'heart',
            ],
            [
                'id' => 'total_abrigados',
                'titulo' => 'Total Abrigados',
                'valor' => (string) $abrigoTotal,
                'legenda' => $legendaAbrigo,
                'legenda_variant' => 'neutral',
                'icon' => 'home',
            ],
        ];
    }

    private function countAnimalsInState(string $nome): int
    {
        $id = AnimalState::query()->where('nome', $nome)->value('id');
        if ($id === null) {
            return 0;
        }

        return Animal::query()->where('animal_state_id', $id)->count();
    }

    private function countCadastrosInRange(Carbon $start, Carbon $end): int
    {
        return Animal::query()
            ->whereBetween('data_ficha', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->count();
    }

    private function countAdoptionsInRange(Carbon $start, Carbon $end): int
    {
        $id = AnimalState::query()->where('nome', self::STATE_ADOTADO)->value('id');
        if ($id === null) {
            return 0;
        }

        return Animal::query()
            ->where('animal_state_id', $id)
            ->whereBetween('animal_state_changed_at', [$start, $end])
            ->count();
    }

    /**
     * Fila: animais em "Esperando consulta", ordenados pelo tempo no estado (mais antigo primeiro).
     *
     * @return list<array{id: string, nome: string, especie: string, estado_nome: string, estado_alterado_em: string|null, data_entrada: string}>
     */
    private function buildFilaAtendimento(): array
    {
        $rows = Animal::query()
            ->with('animalState')
            ->whereHas('animalState', fn (Builder $q) => $q->where('nome', self::STATE_ESPERANDO_CONSULTA))
            ->orderBy('animal_state_changed_at')
            ->orderBy('created_at')
            ->get();

        return $rows->map(function (Animal $animal) {
            $nomeEstado = $animal->animalState?->nome ?? self::STATE_ESPERANDO_CONSULTA;

            return [
                'id' => (string) $animal->id,
                'nome' => $animal->nome,
                'especie' => $animal->especie,
                'estado_nome' => $nomeEstado,
                'estado_alterado_em' => $animal->animal_state_changed_at?->toIso8601String(),
                'data_entrada' => $animal->data_entrada?->format('Y-m-d') ?? '',
            ];
        })->all();
    }

    /**
     * @return list<array{id: string, nome: string, tipo_raca: string, data: string}>
     */
    private function buildCadastrosMes(Carbon $now): array
    {
        $inicioMes = $now->copy()->startOfMonth();
        $fimMes = $now->copy()->endOfMonth();

        $rows = Animal::query()
            ->whereBetween('data_ficha', [$inicioMes->copy()->startOfDay(), $fimMes->copy()->endOfDay()])
            ->orderByDesc('data_ficha')
            ->orderByDesc('created_at')
            ->get();

        return $rows->map(function (Animal $animal) {
            return [
                'id' => (string) $animal->id,
                'nome' => $animal->nome,
                'tipo_raca' => sprintf('%s — %s', $animal->especie, $animal->raca),
                'data' => $animal->data_ficha?->format('d/m') ?? '',
            ];
        })->all();
    }
}
