<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Services\AnimalProtocoloService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnimalProtocoloServiceTest extends TestCase
{
    use RefreshDatabase;

    private AnimalProtocoloService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AnimalProtocoloService;
    }

    #[Test]
    public function primeiro_protocolo_do_ano_comeca_em_mil(): void
    {
        $this->assertSame('1000/2026', $this->service->sugerirParaAno(2026));
    }

    #[Test]
    public function proximo_protocolo_incrementa_sequencia_do_ano(): void
    {
        $this->criarAnimalComProtocolo('1000/2026');
        $this->criarAnimalComProtocolo('1005/2026');

        $this->assertSame('1006/2026', $this->service->sugerirParaAno(2026));
    }

    #[Test]
    public function anos_diferentes_nao_interferem_na_sequencia(): void
    {
        $this->criarAnimalComProtocolo('1010/2025');
        $this->criarAnimalComProtocolo('1099/2026');

        $this->assertSame('1011/2025', $this->service->sugerirParaAno(2025));
        $this->assertSame('1100/2026', $this->service->sugerirParaAno(2026));
    }

    #[Test]
    public function soft_deleted_conta_na_sequencia(): void
    {
        $animal = $this->criarAnimalComProtocolo('1003/2026');
        $animal->delete();

        $this->assertSame('1004/2026', $this->service->sugerirParaAno(2026));
    }

    #[Test]
    public function sugerir_para_data_ficha_usa_ano_da_data(): void
    {
        $this->criarAnimalComProtocolo('1002/2024');

        $this->assertSame('1003/2024', $this->service->sugerirParaDataFicha('2024-08-15'));
    }

    #[Test]
    public function protocolos_antigos_abaixo_de_mil_nao_reduzem_a_sugestao(): void
    {
        $this->criarAnimalComProtocolo('5/2026');

        $this->assertSame('1000/2026', $this->service->sugerirParaAno(2026));
    }

    private function criarAnimalComProtocolo(string $numeroProtocolo): Animal
    {
        return Animal::query()->create([
            'numero_protocolo' => $numeroProtocolo,
            'nome' => 'Teste',
            'raca' => 'SRD',
            'data_ficha' => '2026-01-01',
            'especie' => 'Cachorro',
            'sexo' => 'Macho',
            'idade' => 1,
            'peso' => 5.0,
            'cor' => 'Caramelo',
            'data_entrada' => '2026-01-01',
            'observacoes' => '',
        ]);
    }
}
