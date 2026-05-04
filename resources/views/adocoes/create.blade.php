@extends('welcome')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Nova Adoção</h1>
    <form action="{{ route('adocoes.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="animal_id" class="block text-sm font-medium">Animal</label>
            <select name="animal_id" id="animal_id" class="mt-1 block w-full border-gray-300 rounded-md">
                @foreach($animais as $animal)
                <option value="{{ $animal->id }}">{{ $animal->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="adotante_id" class="block text-sm font-medium">Adotante</label>
            <select name="adotante_id" id="adotante_id" class="mt-1 block w-full border-gray-300 rounded-md">
                @foreach($adotantes as $adotante)
                <option value="{{ $adotante->id }}">{{ $adotante->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="data_adocao" class="block text-sm font-medium">Data Adoção</label>
            <input type="date" name="data_adocao" id="data_adocao" class="mt-1 block w-full border-gray-300 rounded-md" required>
        </div>
        <div class="mb-4">
            <label for="doc_adocao" class="block text-sm font-medium">Documento Adoção</label>
            <input type="text" name="doc_adocao" id="doc_adocao" class="mt-1 block w-full border-gray-300 rounded-md" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Salvar</button>
    </form>
</div>
@endsection