<?php

namespace App\Services;

use App\Models\Lembrete;
use Carbon\Carbon;

class LembreteRecurrenceService
{
    /**
     * @return list<int>
     */
    public function diasAntecedencia(string $tipo): array
    {
        return $tipo === Lembrete::TIPO_WEEKDAY
            ? [2, 1, 0]
            : [7, 3, 2, 1, 0];
    }

    public function proximaOcorrencia(Lembrete $lembrete, ?Carbon $ref = null): ?Carbon
    {
        if (! $lembrete->ativo) {
            return null;
        }

        $ref = $this->startOfDay($ref ?? Carbon::now(config('app.timezone')));
        $anchor = $this->startOfDay(Carbon::parse($lembrete->data));
        $dataFim = $lembrete->data_fim
            ? $this->startOfDay(Carbon::parse($lembrete->data_fim))
            : null;

        $candidate = match ($lembrete->tipo_recorrencia) {
            Lembrete::TIPO_ONCE => $this->proximaOnce($anchor, $ref),
            Lembrete::TIPO_EVERY_N_DAYS => $this->proximaEveryNDays(
                $anchor,
                $ref,
                (int) ($lembrete->intervalo_dias ?? 0)
            ),
            Lembrete::TIPO_WEEKDAY => $this->proximaWeekday(
                $anchor,
                $ref,
                (int) ($lembrete->dia_semana ?? 0)
            ),
            Lembrete::TIPO_DAY_OF_MONTH => $this->proximaDayOfMonth(
                $anchor,
                $ref,
                (int) ($lembrete->dia_mes ?? 1)
            ),
            default => null,
        };

        if ($candidate === null) {
            return null;
        }

        if ($dataFim !== null && $candidate->gt($dataFim)) {
            return null;
        }

        return $candidate;
    }

    public function diasRestantes(?Carbon $proxima, ?Carbon $ref = null): ?int
    {
        if ($proxima === null) {
            return null;
        }

        $ref = $this->startOfDay($ref ?? Carbon::now(config('app.timezone')));

        return (int) $ref->diffInDays($this->startOfDay($proxima), false);
    }

    public function emAlerta(Lembrete $lembrete, ?Carbon $ref = null): bool
    {
        if (! $lembrete->ativo) {
            return false;
        }

        $now = ($ref ?? Carbon::now(config('app.timezone')))
            ->copy()
            ->timezone(config('app.timezone'));

        $proxima = $this->proximaOcorrencia($lembrete, $now);
        $dias = $this->diasRestantes($proxima, $now);

        if ($dias === null || $dias < 0) {
            return false;
        }

        if (! in_array($dias, $this->diasAntecedencia($lembrete->tipo_recorrencia), true)) {
            return false;
        }

        if ($dias === 0 && $this->hasHora($lembrete) && $proxima !== null) {
            return $now->gte($this->scheduledDateTime($proxima, $lembrete->hora));
        }

        return true;
    }

    public function mensagemAlerta(Lembrete $lembrete, ?Carbon $ref = null): ?string
    {
        if (! $this->emAlerta($lembrete, $ref)) {
            return null;
        }

        $proxima = $this->proximaOcorrencia($lembrete, $ref);
        $dias = $this->diasRestantes($proxima, $ref);

        if ($dias === null) {
            return null;
        }

        return match ($dias) {
            7 => 'Falta 1 semana',
            1 => 'Amanhã',
            0 => 'Hoje',
            default => sprintf('Faltam %d dias', $dias),
        };
    }

    private function hasHora(Lembrete $lembrete): bool
    {
        return $lembrete->hora !== null && $lembrete->hora !== '';
    }

    private function scheduledDateTime(Carbon $proxima, mixed $hora): Carbon
    {
        return Carbon::parse(
            $proxima->format('Y-m-d').' '.$hora,
            config('app.timezone')
        );
    }

    private function proximaOnce(Carbon $anchor, Carbon $ref): ?Carbon
    {
        if ($anchor->gte($ref)) {
            return $anchor;
        }

        return null;
    }

    private function proximaEveryNDays(Carbon $anchor, Carbon $ref, int $intervalo): ?Carbon
    {
        if ($intervalo < 1) {
            return null;
        }

        if ($anchor->gte($ref)) {
            return $anchor;
        }

        $daysDiff = (int) $anchor->diffInDays($ref);
        $k = (int) ceil($daysDiff / $intervalo);

        return $anchor->copy()->addDays($k * $intervalo);
    }

    private function proximaWeekday(Carbon $anchor, Carbon $ref, int $diaSemana): ?Carbon
    {
        $start = $ref->gte($anchor) ? $ref->copy() : $anchor->copy();
        $daysUntil = ($diaSemana - $start->dayOfWeek + 7) % 7;

        return $start->copy()->addDays($daysUntil);
    }

    private function proximaDayOfMonth(Carbon $anchor, Carbon $ref, int $diaMes): ?Carbon
    {
        $cursor = ($ref->gte($anchor) ? $ref : $anchor)->copy()->startOfMonth();

        for ($i = 0; $i < 36; $i++) {
            $candidate = $this->dateOnMonth($cursor->year, $cursor->month, $diaMes);

            if ($candidate->gte($anchor) && $candidate->gte($ref)) {
                return $candidate;
            }

            $cursor->addMonthNoOverflow();
        }

        return null;
    }

    private function dateOnMonth(int $year, int $month, int $diaMes): Carbon
    {
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $day = min($diaMes, $daysInMonth);

        return Carbon::create($year, $month, $day)->startOfDay();
    }

    private function startOfDay(Carbon $date): Carbon
    {
        return $date->copy()->startOfDay();
    }
}
