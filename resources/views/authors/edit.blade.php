@extends('layout')
<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</div>
@section('content')
    <div>
        <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
        <form action="{{ route('authors.update', $author->id) }}" method="post">
            @csrf
            @method('PATCH')
            <fieldset>
                <label for="name">Megnevezés</label>
                <input type="text" id="name" name="name" required value="{{ old('name', $author->name) }}"> 
                <label for="nationality">ország</label>
                <input type="text" id="nationality" name="nationality" required value="{{ old('nationality', $author->nationality) }}"> 
                <label for="age">Megnevezés</label>
                <input type="text" id="age" name="age" required value="{{ old('age', $author->age) }}"> 
                <label for="gender">Megnevezés</label>
                <input type="text" id="gender" name="gender" required value="{{ old('gender', $author->gender) }}"> 
            </fieldset>
            <button type="submit">Ment</button>
            <a href="{{ route('authors.index') }}">Mégse</a>
        </form>
    </div>
@endsection