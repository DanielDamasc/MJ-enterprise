<?php

namespace Tests\Feature;

use App\Enums\ServiceStatus;
use App\Jobs\SendWhatsappRemindersJob;
use App\Models\AirConditioning;
use App\Models\Client;
use App\Models\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationSelectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_selects_correct_clients_for_notification(): void
    {
        Bus::fake();

        // --- Cenário 1: DEVE RECEBER (primeiro aviso) ---
        $client1 = Client::factory()
            ->has(AirConditioning::factory()->state([
                'prox_higienizacao' => now()->addDay() // dentro da data
            ]),
                'airConditioners'
            )
            ->create([
                'qtd_notificacoes' => 0,
                'ultima_notificacao' => null
            ]);

        // --- Cenário 2: DEVE RECEBER (segundo aviso) ---
        $client2 = Client::factory()
            ->has(AirConditioning::factory()->state([
                'prox_higienizacao' => now()->addDays(3) // dentro da data
            ]),
                'airConditioners'
            )
            ->create([
                'qtd_notificacoes' => 1,
                'ultima_notificacao' => now()->subMonths(3) // anti-spam, pode
            ]);

        // --- Cenário 3: NÃO DEVE RECEBER (vencimento longe) ---
        $client3 = Client::factory()
            ->has(AirConditioning::factory()->state([
                'prox_higienizacao' => now()->addMonth() // fora da data
            ]),
                'airConditioners'
            )
            ->create([
                'qtd_notificacoes' => 0
            ]);

        // --- Cenário 4: NÃO DEVE RECEBER (vencimento passou) ---
        $client4 = Client::factory()
            ->has(AirConditioning::factory()->state([
                'prox_higienizacao' => now()->subDay() // fora da data, passou
            ]),
                'airConditioners'
            )
            ->create([
                'qtd_notificacoes' => 0
            ]);

        // --- Cenário 5: NÃO DEVE RECEBER (já tem agendamento) ---
        $client5 = Client::factory()
            ->has(AirConditioning::factory()->state([
                'prox_higienizacao' => now()->addDays(2) // dentro da data
            ]),
                'airConditioners'
            )
            ->create(['qtd_notificacoes' => 0]);

        // Já tem um agendamento de higienização.
        OrderService::factory()->create([
            'cliente_id' => $client5->id,
            'status' => ServiceStatus::AGENDADO,
            'tipo' => 'higienizacao',
        ]);

        // --- Cenário 6: NÃO DEVE RECEBER (spam recente) ---
        $client6 = Client::factory()
            ->has(AirConditioning::factory()->state([
                'prox_higienizacao' => now()->addDays(3) // dentro da data
            ]),
                'airConditioners'
            )
            ->create([
                'qtd_notificacoes' => 1,
                'ultima_notificacao' => now()->subWeek() // é spam, não pode
            ]);

        // --- Cenário 7: NÃO DEVE RECEBER (limite excedido) ---
        $client7 = Client::factory()
            ->has(AirConditioning::factory()->state([
                'prox_higienizacao' => now()->addDays(3) // dentro da data
            ]),
                'airConditioners'
            )
            ->create([
                'qtd_notificacoes' => 2, // limite de notificações excedido
                'ultima_notificacao' => now()->subMonths(3)
            ]);

        $this->artisan('app:send-whatsapp-reminders')
            ->assertExitCode(0);

        Bus::assertDispatched(SendWhatsappRemindersJob::class, function ($job) use ($client1) {
            return $job->client->id === $client1->id;
        });

        Bus::assertDispatched(SendWhatsappRemindersJob::class, function ($job) use ($client2) {
            return $job->client->id === $client2->id;
        });

        Bus::assertNotDispatched(SendWhatsappRemindersJob::class, function ($job) use ($client3, $client4, $client5, $client6, $client7) {
            return in_array($job->client->id, [
                $client3->id,
                $client4->id,
                $client5->id,
                $client6->id,
                $client7->id,
            ]);
        });
    }
}
