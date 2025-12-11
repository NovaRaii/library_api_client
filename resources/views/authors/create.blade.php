@extends('layout')

	<main>
        	@yield('content')
    	</main>

	
@section('content')
<h1>Új Szerző</h1>
<div>


<form action="{{ route('authors.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <fieldset>
            <label for="name">Megnevezés</label>
            <input type="text" id="name" name="name">
            <label for="name">Nemzetiség</label>
            <input type="text" id="nationality" name="nationality">
            <label for="name">Életkor</label>
            <input type="text" id="age" name="age">
            <label for="name">Nem</label>
            <input type="text" id="gender" name="gender">
        </fieldset>
        <button type="submit">Ment</button>
        <a href="{{ route('authors.index') }}">Mégse</a>
    </form>
</div>
@endsection