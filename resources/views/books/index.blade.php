@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2xl">Books</h1>

    <form class="flex mb-4 items-center space-x-2" method="GET" action="{{ route('books.index') }}">
        <input class="input" type="text" name="title" placeholder="Search by title"
               value="{{ request('title') }}">
        <input type="hidden" name="filter" value="{{ request('filter') }}">
        <button class="btn" type="submit">Search</button>
        <a class="btn" href="{{ route('books.index') }}">Clear</a>
    </form>

    <div class="filter-container md-4 flex">
        @php
            $filters = array(
                '' => 'Latest',
                'popular_last_month' => 'Popular Last Month',
                'popular_last_six_months' => 'Popular Last Six Months',
                'highest_rated_last_month' => 'Highest Rated Last Month',
                'highest_rated_last_six_months' => 'Highest Rated Last Six Months'
            )
        @endphp

        @foreach($filters as $key => $label)
            <a class="{{ request('filter') === $key || (request('filter') === null && $key === '') ? 'filter-item-active' : 'filter-item' }}"
               href="{{ route('books.index', array(...request()->query(),'filter' => $key)) }}">{{ $label }}</a>
        @endforeach
    </div>

    <ul>
        @forelse($books as $book)
            <li class="mb-4">
                <div class="book-item">
                    <div class="flex flex-wrap items-center justify-between">
                        <div class="w-full flex-grow sm:w-auto">
                            <a href="{{ route('books.show', $book) }}" class="book-title">{{ $book->title }}</a>
                            <span class="book-author">by - {{ $book->author }}</span>
                        </div>
                        <div>
                            <div class="book-rating">
                                {{ number_format($book->reviews_avg_rating, 1) }}
                            </div>
                            <div>
                                out of {{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-text">No books found</p>
                    <a href="{{ route('books.index') }}" class="reset-lint">Reset criteria</a>
                </div>
            </li>
        @endforelse
    </ul>

@endsection
