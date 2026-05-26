<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class RelatorioPdfService
{
    /**
     * @param array{
     *     cadastro_de: string,
     *     cadastro_ate: string,
     *     serie_de: string,
     *     serie_ate: string,
     *     apenas_mes_atual: bool
     * } $filtros
     * @param array{
     *     cadastros_por_mes: list<array{ano_mes: string, total: int}>,
     *     estados_clinica: array{esperando_consulta: int, consultado: int, em_cirurgia: int},
     *     abrigados_adotados_por_mes: list<array{ano_mes: string, abrigados: int, adotados: int}>
     * } $dashboard
     */
    public function streamPdf(array $filtros, array $dashboard): Response
    {
        $html = View::make('relatorio', [
            'filtros' => $filtros,
            'dashboard' => $dashboard,
            'geradoEm' => now()->timezone(config('app.timezone'))->format('d/m/Y H:i'),
        ])->render();

        return Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadHTML($html)
            ->setPaper('A4', 'portrait')
            ->stream('relatorio.pdf');
    }
}
