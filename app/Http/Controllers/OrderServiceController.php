<?php

namespace App\Http\Controllers;

use App\Models\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderServiceController extends Controller
{
    public function gerarPDF($id)
    {
        $os = OrderService::with(['airConditioners', 'client.address', 'user'])->findOrFail($id);

        // Carrega a view passando a variável $os
        $pdf = Pdf::loadView('pdf.ordem-servico', compact('os'));

        // Abre o PDF no navegador ao invés de forçar o download direto
        return $pdf->stream('OS-' . $os->client->cliente . '.pdf');
    }
}
