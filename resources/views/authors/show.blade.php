@extends('layout')

@section('content')
<div class="container">
    <h1>Szerző adatlap</h1>

    <div class="card mt-3">
        <div class="card-body">
            <h3 class="card-title">{{ $author->name }}</h3>

            <p><strong>Nemzetiség:</strong> {{ $author->nationality }}</p>
            <p><strong>Életkor:</strong> {{ $author->age }}</p>
            <p><strong>Nem:</strong> {{ $author->gender }}</p>

            <div class="mt-3">
                <a href="{{ route('authors.index') }}" class="btn btn-secondary">Vissza a listához</a>
        </div>
            <div class="mt-3">
                <a href=" {{ route('books.index', ['search' => $author->name]) }} " class="btn btn-secondary">Könyvei</a>
            </div>
    </div>
</div>
@endsection
