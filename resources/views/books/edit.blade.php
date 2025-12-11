@extends('layout')

@section('content')
<div class="container">
    <h1>Könyv módosítása</h1>

    <form action="{{ route('books.update', $book->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <fieldset class="mb-3">
            <label for="name">Megnevezés</label>
            <input type="text" id="name" name="name" required 
                   value="{{ old('name', $book->name) }}">  

            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" required 
                   value="{{ old('isbn', $book->isbn ?? '') }}">  

            <label for="category_id">Kategória</label>
            <select name="category_id" id="category_id" required>
                <option value="" disabled>-- Válassz kategóriát --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <label for="price">Ár</label>
            <input type="text" id="price" name="price" required 
                   value="{{ old('price', $book->price) }}">  

            <label for="publication_date">Kiadási Év</label>
            <input type="text" id="publication_date" name="publication_date" required 
                   value="{{ old('publication_date', $book->publication_date) }}">  

            <label for="edition">Kiadás</label>
            <input type="text" id="edition" name="edition" required 
                   value="{{ old('edition', $book->edition) }}">  

            <label for="author_id">Szerző</label>
            <select name="author_id" id="author_id" required>
                <option value="" disabled>-- Válassz szerzőt --</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" 
                        {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>

            <label for="cover">Borító</label><br>
            @if($book->cover)
                <img src="{{ asset('covers/' . $book->cover) }}" 
                     alt="{{ $book->name }}" 
                     class="img-fluid mb-2" 
                     style="max-height: 200px;">
            @else
                <div class="mb-2">Nincs borító</div>
            @endif
            <input type="file" id="cover" name="cover" accept="image/*">
        </fieldset>

        <button type="submit" class="btn btn-primary">Ment</button>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">Mégse</a>
    </form>
</div>
@endsection
