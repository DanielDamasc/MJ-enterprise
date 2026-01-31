<?php

namespace App\Console\Commands;

use App\Enums\ServiceStatus;
use App\Jobs\SendWhatsappRemindersJob;
use App\Models\Client;
use App\Models\OrderService;
use Illuminate\Console\Command;
use Log;

class SendWhatsappReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-whatsapp-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica a data da próxima higienização do cliente e se o status é agendado, caso a próxima higienização esteja agendada para os próximos 7 dias, dispara uma mensagem de lembrete para o cliente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // --- VARIÁVEIS AUXILIARES ---

        $start = now()->startOfDay(); // Início é o dia de hoje.
        $end = now()->addDays(7)->endOfDay(); // Fim é hoje adicionados 7 dias.
        $segundoAviso = now()->subMonths(2); // Segundo aviso da higienização.

        // --- LÓGICA DE BUSCA ---

        $this->info("Buscando clientes com data de próxima higienização entre {$start} e {$end}");

        $clientes = Client::where(function ($q) use ($segundoAviso) {
            // 1. Primeira notificação.
            $q->where('qtd_notificacoes', 0)
            ->whereNull('ultima_notificacao')

            // 2. Segunda notificação, depois de 2 meses do primeiro aviso.
            ->orWhere(function ($sub) use ($segundoAviso) {
                $sub->where('qtd_notificacoes', 1)
                    ->where('ultima_notificacao', '<', $segundoAviso); // Já se passaram dois meses
            });
        })
        ->whereHas('airConditioners', function ($q) use ($start, $end) {
            $q->whereBetween('prox_higienizacao', [$start, $end]);
        })
        ->whereDoesntHave('servicos', function ($q) {
            $q->where('status', ServiceStatus::AGENDADO->value)
            ->where('tipo', 'higienizacao');
        })
        ->get();

        // --- ENVIANDO OS CLIENTES PARA O JOB ---

        Log::info("Total de clientes que serão notificados: " . $clientes->count());

        foreach ($clientes as $cliente) {
            SendWhatsappRemindersJob::dispatch($cliente);
        }
    }
}
