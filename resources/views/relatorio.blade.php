<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1d1d1f; }
        h1 { font-size: 18px; color: #6b8e00; margin: 0 0 8px 0; }
        h2 { font-size: 13px; margin: 16px 0 6px 0; border-bottom: 1px solid #c7c7cc; padding-bottom: 4px; }
        .meta { color: #6b7280; margin-bottom: 14px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        th { background: #f4f6f7; font-weight: 600; }
        tfoot th, tfoot td {
            background: #eef1f3;
            font-weight: 700;
            border-top: 2px solid #9ca3af;
        }
        .num { text-align: right; }
        .grid { width: 100%; }
        .grid td { width: 33%; vertical-align: top; }
    </style>
</head>
<body>
    <h1>Relatório — Indicadores</h1>
    <p class="meta">Gerado em {{ $geradoEm }} — período cadastros: {{ $filtros['cadastro_de'] }} a {{ $filtros['cadastro_ate'] }} — série abrigo/adoção: {{ $filtros['serie_de'] }} a {{ $filtros['serie_ate'] }}@if($filtros['apenas_mes_atual']) (somente mês atual)@endif</p>

    <h2>Animais cadastrados por mês</h2>
    <table>
        <thead>
            <tr><th>Mês (AAAA-MM)</th><th class="num">Total</th></tr>
        </thead>
        <tbody>
            @foreach ($dashboard['cadastros_por_mes'] as $row)
                <tr>
                    <td>{{ $row['ano_mes'] }}</td>
                    <td class="num">{{ $row['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th scope="row">Total</th>
                <td class="num">{{ array_sum(array_column($dashboard['cadastros_por_mes'], 'total')) }}</td>
            </tr>
        </tfoot>
    </table>

    <h2>Estados clínicos (situação atual)</h2>
    <table class="grid">
        <tr>
            <td><strong>Esperando consulta</strong><br>{{ $dashboard['estados_clinica']['esperando_consulta'] }}</td>
            <td><strong>Consultado</strong><br>{{ $dashboard['estados_clinica']['consultado'] }}</td>
            <td><strong>Em cirurgia</strong><br>{{ $dashboard['estados_clinica']['em_cirurgia'] }}</td>
        </tr>
    </table>

    <h2>Abrigados x adotados por mês</h2>
    <p style="color:#6b7280;font-size:10px;margin:4px 0 8px 0;">Abrigados: data_ficha no mês e estado atual diferente de Adotado. Adotados: registros de adoção por data_adocao.</p>
    <table>
        <thead>
            <tr><th>Mês</th><th class="num">Abrigados</th><th class="num">Adotados</th></tr>
        </thead>
        <tbody>
            @foreach ($dashboard['abrigados_adotados_por_mes'] as $row)
                <tr>
                    <td>{{ $row['ano_mes'] }}</td>
                    <td class="num">{{ $row['abrigados'] }}</td>
                    <td class="num">{{ $row['adotados'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th scope="row">Total</th>
                <td class="num">{{ array_sum(array_column($dashboard['abrigados_adotados_por_mes'], 'abrigados')) }}</td>
                <td class="num">{{ array_sum(array_column($dashboard['abrigados_adotados_por_mes'], 'adotados')) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
