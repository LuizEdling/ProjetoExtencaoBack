<?php

namespace Tests\Unit;

use App\Models\Lembrete;
use App\Services\LembreteRecurrenceService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LembreteRecurrenceServiceTest extends TestCase
{
    private LembreteRecurrenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LembreteRecurrenceService;
    }

    #[Test]
    public function once_no_passado_nao_tem_proxima_ocorrencia(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_ONCE,
            'data' => '2026-01-01',
            'ativo' => true,
        ]);

        $ref = Carbon::parse('2026-06-09');

        $this->assertNull($this->service->proximaOcorrencia($lembrete, $ref));
        $this->assertFalse($this->service->emAlerta($lembrete, $ref));
    }

    #[Test]
    public function once_futuro_alerta_em_sete_tres_dois_e_um_dia(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_ONCE,
            'data' => '2026-06-16',
            'ativo' => true,
        ]);

        $this->assertTrue($this->service->emAlerta($lembrete, Carbon::parse('2026-06-09')));
        $this->assertTrue($this->service->emAlerta($lembrete, Carbon::parse('2026-06-13')));
        $this->assertTrue($this->service->emAlerta($lembrete, Carbon::parse('2026-06-14')));
        $this->assertTrue($this->service->emAlerta($lembrete, Carbon::parse('2026-06-15')));
        $this->assertTrue($this->service->emAlerta($lembrete, Carbon::parse('2026-06-16')));
        $this->assertFalse($this->service->emAlerta($lembrete, Carbon::parse('2026-06-10')));
    }

    #[Test]
    public function once_no_dia_sem_hora_dispara_alerta(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_ONCE,
            'data' => '2026-06-09',
            'ativo' => true,
        ]);

        $ref = Carbon::parse('2026-06-09 10:00:00', config('app.timezone'));

        $this->assertTrue($this->service->emAlerta($lembrete, $ref));
        $this->assertSame('Hoje', $this->service->mensagemAlerta($lembrete, $ref));
    }

    #[Test]
    public function once_no_dia_com_hora_so_alerta_apos_horario(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_ONCE,
            'data' => '2026-06-09',
            'hora' => '22:31',
            'ativo' => true,
        ]);

        $antes = Carbon::parse('2026-06-09 22:30:00', config('app.timezone'));
        $naHora = Carbon::parse('2026-06-09 22:31:00', config('app.timezone'));
        $depois = Carbon::parse('2026-06-09 22:33:00', config('app.timezone'));

        $this->assertFalse($this->service->emAlerta($lembrete, $antes));
        $this->assertTrue($this->service->emAlerta($lembrete, $naHora));
        $this->assertTrue($this->service->emAlerta($lembrete, $depois));
        $this->assertSame('Hoje', $this->service->mensagemAlerta($lembrete, $depois));
    }

    #[Test]
    public function weekday_alerta_apenas_dois_e_um_dia_antes(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_WEEKDAY,
            'dia_semana' => 2,
            'data' => '2026-06-01',
            'ativo' => true,
        ]);

        $ref = Carbon::parse('2026-06-08');

        $this->assertEquals('2026-06-09', $this->service->proximaOcorrencia($lembrete, $ref)?->format('Y-m-d'));
        $this->assertFalse($this->service->emAlerta($lembrete, Carbon::parse('2026-06-06')));
        $this->assertTrue($this->service->emAlerta($lembrete, Carbon::parse('2026-06-07')));
        $this->assertTrue($this->service->emAlerta($lembrete, $ref));
        $this->assertTrue($this->service->emAlerta($lembrete, Carbon::parse('2026-06-09 12:00:00', config('app.timezone'))));
    }

    #[Test]
    public function every_n_days_calcula_proxima_data(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_EVERY_N_DAYS,
            'intervalo_dias' => 3,
            'data' => '2026-06-01',
            'ativo' => true,
        ]);

        $ref = Carbon::parse('2026-06-05');

        $this->assertEquals('2026-06-07', $this->service->proximaOcorrencia($lembrete, $ref)?->format('Y-m-d'));
    }

    #[Test]
    public function day_of_month_ajusta_para_mes_curto(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_DAY_OF_MONTH,
            'dia_mes' => 31,
            'data' => '2026-01-01',
            'ativo' => true,
        ]);

        $ref = Carbon::parse('2026-02-01');

        $this->assertEquals('2026-02-28', $this->service->proximaOcorrencia($lembrete, $ref)?->format('Y-m-d'));
    }

    #[Test]
    public function mensagem_alerta_retorna_texto_correto(): void
    {
        $lembrete = new Lembrete([
            'tipo_recorrencia' => Lembrete::TIPO_ONCE,
            'data' => '2026-06-16',
            'ativo' => true,
        ]);

        $this->assertSame('Falta 1 semana', $this->service->mensagemAlerta($lembrete, Carbon::parse('2026-06-09')));
        $this->assertSame('Amanhã', $this->service->mensagemAlerta($lembrete, Carbon::parse('2026-06-15')));
        $this->assertSame('Faltam 3 dias', $this->service->mensagemAlerta($lembrete, Carbon::parse('2026-06-13')));
    }
}
