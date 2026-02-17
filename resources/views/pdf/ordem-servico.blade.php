<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço #{{ $os->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .section-title { padding: 5px; font-weight: bold; margin-top: 15px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table th, .info-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .info-table th { background-color: #f9fafb; width: 25%; }
        .watermark {
            position: fixed;
            top: 10%;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: -1000;
            opacity: 0.10;
        }
        .watermark img {
            height: 800px;
        }
    </style>
</head>
<body>

    <div class="watermark">
        {{-- Use public_path() para o DomPDF conseguir ler o arquivo localmente no servidor --}}
        <img src="{{ public_path('img/marcadaguaMJ.jpg') }}" alt="Marca d'água">
    </div>

    <div class="header">
        <h1>Ordem de Serviço nº {{ str_pad($os->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <p>Data de Exportação: {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">Dados do Cliente</div>
    <table class="info-table">
        <tr>
            <th>Nome/Razão Social:</th>
            <td colspan="3">{{ $os->client->cliente }}</td>
        </tr>
        <tr>
            <th>Endereço:</th>
            <td colspan="3">{{ $os->client->address->endereco ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Telefone:</th>
            <td colspan="3">{{ $os->client->telefone }}</td>
        </tr>
        <tr>
            <th>Tipo:</th>
            <td colspan="3">{{ ucfirst($os->client->tipo) }}</td>
        </tr>
    </table>

    <br>

    <div class="section-title">Equipamentos (Ar-Condicionado)</div>
    <table class="info-table" style="table-layout: fixed; word-wrap: break-word;">
        <thead>
            <tr>
                <th style="width: 10%;">Cod</th>
                <th style="width: 20%;">Marca / Tipo</th>
                <th style="width: 15%;">Potência</th>
                <th style="width: 40%;">Ambiente</th>
                <th style="width: 15%;">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($os->airConditioners as $ac)
                <tr>
                    <td>{{ $ac->codigo_ac }}</td>
                    <td>{{ $ac->marca ? $ac->marca : 'N/A' }} / {{ ucfirst($ac->tipo) }}</td>
                    <td>{{ $ac->potencia }} BTUs</td>
                    <td>{{ $ac->ambiente ? $ac->ambiente : 'N/A' }}</td>
                    <td>R$ {{ number_format($ac->pivot->valor, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <div class="section-title">Dados do Serviço</div>
    <table class="info-table">
        <tr>
            <th>Tipo do Serviço:</th>
            <td colspan="3">{{ $os->tipo_label }}</td>
        </tr>
        <tr>
            <th>Data de Execução:</th>
            <td>{{ \Carbon\Carbon::parse($os->data_servico)->format('d/m/Y') }}</td>
            <th>Horário:</th>
            <td>{{ $os->horario ? $os->horario : '--:--' }}</td>
        </tr>
        <tr>
            <th>Valor Total:</th>
            <td colspan="3">R$ {{ number_format($os->total, 2, ',', '.') }}</td>
        </tr>
    </table>

    <br><br><br>

    <div style="text-align: center; margin-top: 60px;">

        <div style="width: 350px; margin: 0 auto; border-top: 1px solid #333; padding-top: 5px;">
            Assinatura do Técnico Responsável
        </div>

        <br><br><br><br>

        <div style="width: 350px; margin: 0 auto; border-top: 1px solid #333; padding-top: 5px;">
            Assinatura do Cliente
        </div>

    </div>

</body>
</html>
