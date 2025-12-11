@extends('layout')
<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</div>
@section('content')
    <div>
        <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
        <form action="{{ route('categories.update', $category->id) }}" method="post">
            @csrf
            @method('PATCH')
            <fieldset>
                <label for="name">Megnevezés</label>
                <input type="text" id="name" name="name" required value="{{ old('name', $category->name) }}"> 
            </fieldset>
            <button type="submit">Ment</button>
            <a href="{{ route('categories.index') }}">Mégse</a>
        </form>
    </div>
@endsection