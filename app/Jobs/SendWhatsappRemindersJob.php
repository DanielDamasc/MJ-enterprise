<?php

namespace App\Jobs;

use App\Enums\ServiceStatus;
use App\Models\ActivityLog;
use App\Models\Client;
use Carbon\Carbon;
use Exception;
use Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Str;

class SendWhatsappRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // quantidade de tentativas em caso de erro.
    public $backoff = 60; // tempo de espera entre tentativas.

    /**
     * Create a new job instance.
     */
    public function __construct(public Client $client)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Dados da API.
        $token = config('services.whatsapp.token', env('WHATSAPP_TOKEN'));
        $phoneId = config('services.whatsapp.phone_id', env('WHATSAPP_PHONE_ID'));
        $version = 'v22.0';

        // Dados do template.
        $nome = Str::of($this->client->contato)->explode(' ')->first();
        $to = $this->normalizarTelefone($this->client->telefone);
        $meses = (string) $this->calcularUltimaHigienizacao() . ' meses';

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => 'lembrete_proxima_higienizacao', // Nome do modelo
                'language' => [
                    'code' => 'pt_BR' // Língua da mensagem
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $nome,
                            ],
                            [
                                'type' => 'text',
                                'text' => $meses,
                            ]
                        ]
                    ]
                ],
            ]
        ];

        Log::info("JOB [Cliente {$this->client->id}]: Enviando Payload...", $payload);

        $response = Http::withToken($token)
        ->post("https://graph.facebook.com/{$version}/{$phoneId}/messages", $payload);

        Log::info("
            JOB [Cliente {$this->client->id}]:
            Resposta da API ({$response->status()})",$response->json()
        );

        if ($response->successful()) {

            // Atualiza os dados de notificação do cliente.
            $this->client->update([
                'ultima_notificacao' => now(),
                'qtd_notificacoes' => $this->client->qtd_notificacoes + 1
            ]);

            // Cria um registro de logs para cada notificação realizada.
            ActivityLog::create([
                'log_name'     => 'notificacao_whatsapp',
                'description'  => 'Lembrete de higienização enviado via WhatsApp API',
                'event'        => 'sent',

                // Vincula ao Cliente (Subject)
                'subject_type' => Client::class,
                'subject_id'   => $this->client->id,

                // Define quem fez (Ninguém = Sistema)
                'causer_type'  => null,
                'causer_id'    => null,

                // Dados extras (Payload)
                'properties'   => [
                    'telefone_destino' => $to,
                    'meses_calculados' => $meses,
                    'status_api'       => 'sucesso'
                ],
            ]);

        } else {
            Log::error("Erro na API do Whatsapp: " . $response->body());
            throw new Exception("Erro na API do Whatsapp: " . $response->body());
        }
    }

    private function normalizarTelefone($tel)
    {
        if (strlen($tel) <= 11) {
            return '55' . $tel;
        }

        return $tel;
    }

    private function calcularUltimaHigienizacao()
    {
        $ultimoServico = $this->client->servicos()
            ->where('status', ServiceStatus::CONCLUIDO->value)
            ->where('tipo', 'higienizacao')
            ->latest('data_servico')
            ->first();

        if ($ultimoServico && $ultimoServico->data_servico) {
            $data = Carbon::parse($ultimoServico->data_servico);

            $diff = (int) ceil($data->floatDiffInMonths(now()));

            return $diff;
        }

        // Se nunca fez serviço, assume-se que é 6 meses por padrão.
        return 6;
    }
}
