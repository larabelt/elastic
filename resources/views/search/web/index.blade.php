@extends('belt-core::layouts.web.main')

@section('main')

    <div class="container">

        <h1>Search Results</h1>

        @foreach($paginators as $paginator)
            @foreach($paginator->items() as $item)
                @include('belt-content::search.web._index')
            @endforeach
        @endforeach

        @if($pager)
            {{ $pager }}
        @endif

    </div>

@endsection