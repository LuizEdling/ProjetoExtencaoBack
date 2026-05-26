<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RelatorioFiltrosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cadastro_de' => ['required', 'date_format:Y-m'],
            'cadastro_ate' => ['required', 'date_format:Y-m'],
            'serie_de' => ['required', 'date_format:Y-m'],
            'serie_ate' => ['required', 'date_format:Y-m'],
            'apenas_mes_atual' => ['sometimes', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array{
     *     cadastro_de: string,
     *     cadastro_ate: string,
     *     serie_de: string,
     *     serie_ate: string,
     *     apenas_mes_atual: bool
     * }
     */
    public function filtrosNormalizados(): array
    {
        $cadastroDe = (string) $this->validated('cadastro_de');
        $cadastroAte = (string) $this->validated('cadastro_ate');
        if ($cadastroDe > $cadastroAte) {
            [$cadastroDe, $cadastroAte] = [$cadastroAte, $cadastroDe];
        }

        $apenasMesAtual = (int) $this->input('apenas_mes_atual', 0) === 1;
        $mesAtual = Carbon::now()->format('Y-m');

        if ($apenasMesAtual) {
            $serieDe = $mesAtual;
            $serieAte = $mesAtual;
        } else {
            $serieDe = (string) $this->validated('serie_de');
            $serieAte = (string) $this->validated('serie_ate');
            if ($serieDe > $serieAte) {
                [$serieDe, $serieAte] = [$serieAte, $serieDe];
            }
        }

        return [
            'cadastro_de' => $cadastroDe,
            'cadastro_ate' => $cadastroAte,
            'serie_de' => $serieDe,
            'serie_ate' => $serieAte,
            'apenas_mes_atual' => $apenasMesAtual,
        ];
    }
}
