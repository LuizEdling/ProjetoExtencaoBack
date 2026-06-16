<?php

namespace App\Http\Controllers;

use App\Http\Resources\LembreteResource;
use App\Models\Lembrete;
use App\Services\LembreteRecurrenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LembreteController extends Controller
{
    public function __construct(
        protected LembreteRecurrenceService $recurrence,
    ) {}

    public function index(): JsonResponse
    {
        $lembretes = Lembrete::query()
            ->get()
            ->sortBy(function (Lembrete $lembrete) {
                $proxima = $this->recurrence->proximaOcorrencia($lembrete);

                return $proxima?->format('Y-m-d') ?? '9999-12-31';
            })
            ->values();

        return response()->json(LembreteResource::collection($lembretes)->resolve());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateLembrete($request);
        $lembrete = Lembrete::create($data);

        return response()->json(new LembreteResource($lembrete), 201);
    }

    public function update(Request $request, Lembrete $lembrete): JsonResponse
    {
        $data = $this->validateLembrete($request, true);
        $lembrete->update($data);

        return response()->json(new LembreteResource($lembrete->fresh()));
    }

    public function destroy(Lembrete $lembrete)
    {
        $lembrete->delete();

        return response()->noContent();
    }

    /**
     * @return array<string, mixed>
     */
    private function validateLembrete(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'data' => ['required', 'date', 'date_format:Y-m-d'],
            'tipo_recorrencia' => ['required', Rule::in(Lembrete::TIPOS_RECORRENCIA)],
            'intervalo_dias' => ['nullable', 'integer', 'min:1', 'required_if:tipo_recorrencia,every_n_days'],
            'dia_semana' => ['nullable', 'integer', 'between:0,6', 'required_if:tipo_recorrencia,weekday'],
            'dia_mes' => ['nullable', 'integer', 'between:1,31', 'required_if:tipo_recorrencia,day_of_month'],
            'data_fim' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:data'],
            'hora' => ['nullable', 'date_format:H:i'],
            'ativo' => ['sometimes', 'boolean'],
        ];

        if ($isUpdate) {
            $rules['visualizado'] = ['sometimes', 'boolean'];
        }

        $data = $request->validate($rules);
        $data = $this->normalizeRecurrenceFields($data);

        if (! $isUpdate) {
            $data['ativo'] = $data['ativo'] ?? true;
            $data['visualizado'] = false;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeRecurrenceFields(array $data): array
    {
        $tipo = $data['tipo_recorrencia'];

        if ($tipo !== Lembrete::TIPO_EVERY_N_DAYS) {
            $data['intervalo_dias'] = null;
        }

        if ($tipo !== Lembrete::TIPO_WEEKDAY) {
            $data['dia_semana'] = null;
        }

        if ($tipo !== Lembrete::TIPO_DAY_OF_MONTH) {
            $data['dia_mes'] = null;
        }

        return $data;
    }
}
