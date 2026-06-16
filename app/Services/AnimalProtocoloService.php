<?php

namespace App\Services;

use App\Models\Animal;

class AnimalProtocoloService
{
    private const SEQUENCIA_INICIAL = 1000;

    public function sugerirParaDataFicha(string $dataFicha): string
    {
        $ano = (int) substr($dataFicha, 0, 4);
        if ($ano < 1900 || $ano > 2100) {
            $ano = (int) now()->format('Y');
        }

        return $this->sugerirParaAno($ano);
    }

    public function sugerirParaAno(int $ano): string
    {
        $suffix = '/'.$ano;

        $max = Animal::withTrashed()
            ->whereNotNull('numero_protocolo')
            ->where('numero_protocolo', 'like', '%'.$suffix)
            ->pluck('numero_protocolo')
            ->map(function (?string $protocolo) use ($ano) {
                if ($protocolo === null || ! str_ends_with($protocolo, '/'.$ano)) {
                    return 0;
                }

                $parts = explode('/', $protocolo, 2);

                return (int) ($parts[0] ?? 0);
            })
            ->max() ?? 0;

        $next = max(self::SEQUENCIA_INICIAL, $max + 1);

        return $next.'/'.$ano;
    }
}
