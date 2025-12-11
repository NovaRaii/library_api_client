l@extends('layout')
 
@section('content')
<h1>Könyvek</h1>

<form method="GET" action="{{ route('books.index') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Keresés...">
    <button type="submit">Keresés</button>

    <select name="category" onchange="this.form.submit()">
        <option value="">-- Összes kategória --</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $cat->id == request('category') ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</form>

<div>
    <a href="{{ route('books.create') }}" title="Új">Új hozzáadása</a>

    <table border="1" cellpadding="5" cellspacing="0" style="width:100%; margin-top:10px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Borító</th>
                <th>Könyv & Szerző</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
                <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                    <td>{{ $book->id }}</td>
                    <td>
                        @if($book->cover)
                            <img src="{{ asset('covers/' . $book->cover) }}" alt="{{ $book->name }}" style="height:60px;">
                        @else
                            <span>No cover</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('books.show', $book->id) }}">
                            {{ $authors->firstWhere('id', $book->author_id)->name ?? 'Ismeretlen szerző' }} – {{ $book->name }}
                        </a>
                        <br>
                        <br>
                        <small>Kategória: {{ $categories->firstWhere('id', $book->category_id)->name ?? 'Ismeretlen' }}</small>
                    </td>
                    <td>
                        <a href="{{ route('books.edit', $book->id) }}"><button>Módosít</button></a>
                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" 
                              onsubmit="return confirm('Biztos törlöd?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Töröl</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{-- {{ $books->appends(request()->query())->links() }} --}}
        {{-- Pagination not supported by API yet --}}
    </div>
</div>

<style>
    .even { background-color: #f9f9f9; }
    .odd { background-color: #ffffff; }

    /* Hide the Previous and Next arrows */
    .pagination .page-item:first-child,
    .pagination .page-item:last-child {
        display: none;
    }
</style>
@endsection
