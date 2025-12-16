@extends('layout')

@section('content')
<div class="welcome-container" style="text-align: center; padding: 50px;">
    <h1>Üdvözöljük a Könyvtárban!</h1>
    <p>Válasszon az alábbi lehetőségek közül:</p>
    
    <div style="margin-top: 30px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
        <a href="{{ route('authors.index') }}" style="padding: 20px 40px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-size: 1.2em; min-width: 200px;">
            <i class="fa fa-users" style="display: block; font-size: 2em; margin-bottom: 10px;"></i>
            Szerzők
        </a>
        <a href="{{ route('books.index') }}" style="padding: 20px 40px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-size: 1.2em; min-width: 200px;">
            <i class="fa fa-book" style="display: block; font-size: 2em; margin-bottom: 10px;"></i>
            Könyvek
        </a>
        <a href="{{ route('categories.index') }}" style="padding: 20px 40px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-size: 1.2em; min-width: 200px;">
            <i class="fa fa-tags" style="display: block; font-size: 2em; margin-bottom: 10px;"></i>
            Kategóriák
        </a>
    </div>
</div>
@endsection
