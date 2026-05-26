<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Termo de Responsabilidade - {{ $animal->nome ?? $animal->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .linha-dado {
            margin-bottom: 12px;
        }
        .grid-checkbox {
            margin-bottom: 12px;
        }
        .checkbox-item {
            display: inline-block;
            width: 48%;
            margin-bottom: 5px;
        }
        p {
            text-align: justify;
            margin-top: 15px;
            margin-bottom: 15px;
            text-indent: 30px;
        }
        .data-local {
            text-align: right;
            margin-top: 40px;
            margin-bottom: 50px;
        }
        .tabela-assinaturas {
            width: 100%;
            margin-top: 100px; /* Aumentamos de 40px para 100px para dar um baita espaço acima */
            border-collapse: collapse;
        }
        .tabela-assinaturas td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 30px; /* Espaço extra dentro da célula, se precisar */
        }
        .tabela-assinaturas td strong {
            display: inline-block;
            margin-top: 8px; /* Dá um respiro entre a linha ______ e o "Nome do Adotante" */
        }
    </style>
</head>
<body>

    <h1>Termo de Responsabilidade</h1>

    <div class="linha-dado"><strong>Nome do Responsável:</strong> {{ $adotante->nome ?? '___________________________________________' }}</div>
    <div class="linha-dado">
        <strong>RG:</strong> {{ $adotante->rg ?? '____________________' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <strong>CPF:</strong> {{ $adotante->cpf ?? '____________________' }}
    </div>
    <div class="linha-dado"><strong>Endereço:</strong> {{ $adotante->endereco ?? '___________________________________________' }}</div>
    <div class="linha-dado">
        <strong>Bairro:</strong> {{ $adotante->bairro ?? '____________________' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <strong>Cidade/UF:</strong> 
        @if(isset($adotante->cidade) && isset($adotante->uf))
            {{ $adotante->cidade }}/{{ $adotante->uf }}
        @else
            ____________________
        @endif
    </div>
    <div class="linha-dado">
        <strong>Contato:</strong> {{ $adotante->contato ?? $adotante->telefone ?? '____________________' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>

    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

    <div class="linha-dado"><strong>Identificação do animal (Nome / ID):</strong> {{ $animal->nome }} (ID: #{{ str_pad($animal->id, 4, '0', STR_PAD_LEFT) }})</div>
    
    <div class="grid-checkbox">
        <div class="checkbox-item">
            <strong>Espécie animal:</strong> 
            ( {!! $animal->especie === 'Cão' ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Cão &nbsp;&nbsp; 
            ( {!! $animal->especie === 'Gato' ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Gato
        </div>
        <div class="checkbox-item">
            <strong>Sexo:</strong> 
            ( {!! $animal->sexo === 'Macho' ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Macho &nbsp;&nbsp; 
            ( {!! $animal->sexo === 'Fêmea' ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Fêmea
        </div>
        <div class="checkbox-item">
            <strong>Castrado:</strong> 
            ( {!! $animal->castrado ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Sim &nbsp;&nbsp; 
            ( {!! ! $animal->castrado ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Não
        </div>
        <div class="checkbox-item">
            <strong>Categoria:</strong> 
            ( {!! $animal->idade == 0 ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Filhote &nbsp;&nbsp; 
            ( {!! $animal->idade > 0 ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Adulto
        </div>
        <div class="checkbox-item">
            <strong>Vermifugado:</strong> 
            ( {!! $animal->vermifugado ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Sim &nbsp;&nbsp; 
            ( {!! ! $animal->vermifugado ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Não
        </div>
        <div class="checkbox-item">
            <strong>Vacinado:</strong> 
            ( {!! $animal->vacinado ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Sim &nbsp;&nbsp; 
            ( {!! ! $animal->vacinado ? 'X' : '<span style="color: #fff;">X</span>' !!} ) Não
        </div>
    </div>

    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

    <p>O ADOTANTE declara ser maior de 18 anos, e que ao assinar este termo de adoção, após sua leitura completa, está ciente de que a partir da ADOÇÃO, a responsabilidade pelo Animal Adotado e os respectivos encargos financeiros com alimentação, vacinação, tratamento, cirurgias, consultas médico-veterinárias, são exclusivamente do(a) ADOTANTE. Compromete-se a manter o animal em ambiente limpo, arejado e espaçoso, com possibilidade de abrigo livre das intempéries climáticas; Não mante-lo preso em espaços pequenos ou em correntes que impeçam sua circulação; Na hipótese de não ter sido o ADOTADO ainda esterilizado (castrado), por não ter idade suficiente para tanto, o ADOTANTE fica obrigado a esterilizá-lo. Compromete-se ainda a levar o ADOTADO para consulta veterinária, quando necessária, bem como as respectivas doses das vacinas do polivalente e anti-rábica, e posterior revacinação anual.</p>

    <p>A partir da data de adoção o Canil Municipal se isenta da responsabilidade da sanidade do animal adotado, transferindo assim responsabilidade total para o adotante.</p>

    <p>Eu, <strong>{{ $adotante->nome ?? '____________________________________' }}</strong>, portador do RG <strong>{{ $adotante->rg ?? '____________________' }}</strong>, declaro que serei responsável pelo seu bem-estar durante toda sua vida, estando ciente das normas citadas acima, as quais aceito, assinando o Termo de Responsabilidade, assumindo plenamente os deveres que nele constam; O não cumprimento dos itens acima citados poderá ser interpretado como maus-tratos, bem como outros relacionados a posse responsável, acarretará na punição do responsável pelo animal.</p>

    <p>Assim comprometo-me a permitir o acesso ao local onde se encontra o animal para averiguação de suas condições.</p>

    <p>Maus-tratos é crime e estarei sujeito às sanções previstas pela Lei Federal de Crimes Ambientais nº 9605/98 e Decreto Federal 6514/08, no caso ofensa/infração.</p>

    <div class="data-local">
        Guarapuava, {{ now()->locale('pt-BR')->translatedFormat('d \d\e F \d\e Y') }}.
    </div>

    <table class="tabela-assinaturas">
        <tr>
            <td>
                _______________________________________<br>
                <strong>Nome do Doador</strong>
            </td>
            <td>
                _______________________________________<br>
                <strong>Assinatura do Adotante</strong>
            </td>
        </tr>
    </table>

</body>
</html>