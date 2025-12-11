@extends('layout')

@section('content')
<div class="container">
    <h1>Könyv adatlap</h1>

    <div class="card mt-3">
        <div class="card-body text-center">
            @if($book->cover)
                <img src="{{ asset('covers/' . $book->cover) }}" 
                     alt="{{ $book->name }}" 
                     class="img-fluid mb-3" 
                     style="max-height: 300px;">
            @else
                <div class="mb-3">Nincs borító</div>
            @endif

            <h3 class="card-title">{{ $book->name }}</h3>

            <p><strong>Kategória:</strong> {{ $book->category->name ?? 'Ismeretlen' }}</p>
            <p><strong>Ár:</strong> {{ $book->price }} USD</p>
            <p><strong>Kiadási év:</strong> {{ $book->publication_date }}</p>
            <p><strong>Kiadás:</strong> {{ $book->edition }}</p>
            <p><strong>Író:</strong> {{ $book->author->name ?? 'Ismeretlen szerző' }}</p>
            <p><strong>ISBN:</strong> {{ $book->isbn }}</p>

            <div class="mt-3">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Vissza a listához</a>
            </div>
        </div>
    </div>
</div>
@endsection
