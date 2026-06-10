<?php

namespace App\Http\Resources;

use App\Models\Lembrete;
use App\Services\LembreteRecurrenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Lembrete */
class LembreteResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var LembreteRecurrenceService $recurrence */
        $recurrence = app(LembreteRecurrenceService::class);

        $proxima = $recurrence->proximaOcorrencia($this->resource);
        $diasRestantes = $recurrence->diasRestantes($proxima);
        $emAlerta = $recurrence->emAlerta($this->resource);
        $mensagemAlerta = $recurrence->mensagemAlerta($this->resource);

        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao ?? '',
            'data' => $this->data?->format('Y-m-d'),
            'hora' => $this->formatHora(),
            'tipo_recorrencia' => $this->tipo_recorrencia ?? Lembrete::TIPO_ONCE,
            'intervalo_dias' => $this->intervalo_dias,
            'dia_semana' => $this->dia_semana,
            'dia_mes' => $this->dia_mes,
            'data_fim' => $this->data_fim?->format('Y-m-d'),
            'ativo' => (bool) ($this->ativo ?? true),
            'visualizado' => (bool) $this->visualizado,
            'proxima_data' => $proxima?->format('Y-m-d'),
            'dias_restantes' => $diasRestantes,
            'em_alerta' => $emAlerta,
            'mensagem_alerta' => $mensagemAlerta,
        ];
    }

    private function formatHora(): ?string
    {
        if ($this->hora === null || $this->hora === '') {
            return null;
        }

        return Carbon::parse($this->hora)->format('H:i');
    }
}
