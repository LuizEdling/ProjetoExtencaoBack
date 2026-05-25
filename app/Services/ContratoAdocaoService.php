<?php

namespace App\Services;

use App\Models\Adocao;
use App\Models\Contratacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class ContratoAdocaoService
{
    /**
     * Renderiza o termo, persiste o HTML em `contratacao` e devolve resposta PDF (inline).
     */
    public function streamPdf(Adocao $adocao): Response
    {
        $adocao->loadMissing(['animal', 'adotante']);

        $animal = $adocao->animal;
        $adotante = $adocao->adotante;

        $animal->setAttribute(
            'created_at_formatado',
            $animal->created_at?->format('d/m/Y') ?? ''
        );

        $data = [
            'animal' => $animal,
            'adotante' => $adotante,
        ];

        $html = View::make('contrato', $data)->render();

        Contratacao::query()->updateOrCreate(
            ['adocao_id' => $adocao->id],
            ['html_gerado' => $html],
        );

        return Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadHTML($html)
            ->setPaper('A4', 'portrait')
            ->stream("contrato_adocao_{$adocao->id}.pdf");
    }
}
