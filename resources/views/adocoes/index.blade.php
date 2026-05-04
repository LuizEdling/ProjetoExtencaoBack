@extends('welcome')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Adoções</h1>
    <a href="{{ route('adocoes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Nova Adoção</a>
    <table class="table-auto w-full mt-4">
        <thead>
            <tr>
                <th class="px-4 py-2">Animal</th>
                <th class="px-4 py-2">Adotante</th>
                <th class="px-4 py-2">Data Adoção</th>
                <th class="px-4 py-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adocoes as $adocao)
            <tr>
                <td class="border px-4 py-2">{{ $adocao->animal->nome }}</td>
                <td class="border px-4 py-2">{{ $adocao->adotante->nome }}</td>
                <td class="border px-4 py-2">{{ $adocao->data_adocao }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('adocoes.show', $adocao) }}" class="text-blue-500">Ver</a>
                    <form action="{{ route('adocoes.destroy', $adocao) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500">Excluir</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection