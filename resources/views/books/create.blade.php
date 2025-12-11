@extends('layout')

@section('content')
<div class="container">
    <h1>Új könyv hozzáadása</h1>

    <form action="{{ route('books.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <fieldset class="mb-3">
            <label for="name">Megnevezés</label>
            <input type="text" id="name" name="name" required 
                   value="{{ old('name') }}">  

            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" required 
                   value="{{ old('isbn', $suggestedIsbn ?? '') }}">  

            <label for="category_id">Kategória</label>
            <select name="category_id" id="category_id" required>
                <option value="" disabled selected>-- Válassz kategóriát --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <label for="price">Ár</label>
            <input type="text" id="price" name="price" required 
                   value="{{ old('price') }}">  

            <label for="publication_date">Kiadási Év</label>
            <input type="text" id="publication_date" name="publication_date" required 
                   value="{{ old('publication_date') }}">  

            <label for="edition">Kiadás</label>
            <input type="text" id="edition" name="edition" required 
                   value="{{ old('edition') }}">  

            <label for="author_id">Szerző</label>
            <select name="author_id" id="author_id" required>
                <option value="" disabled selected>-- Válassz szerzőt --</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" 
                        {{ old('author_id') == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>

            <label for="cover">Borító</label><br>
            <input type="file" id="cover" name="cover" accept="image/*">
        </fieldset>

        <button type="submit" class="btn btn-primary">Ment</button>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">Mégse</a>
    </form>
</div>
@endsection
