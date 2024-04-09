<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when($title, function ($query, $title) {
            return $query->title($title);
        })->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('updated_at', 'desc');

        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_six_months' => $books->popularLastSixMonths(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_six_months' => $books->highestRatedLastSixMonths(),
            default => $books->latest()
        };

        $books = $books->get();

        return view('books.index', array('books' => $books));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::with(array(
            'reviews' => fn($query) => $query->latest()
        ))->withReviewsCount()->withReviewsAverageRating()->findOrFail($id);
        return view('books.book', array('book' => $book));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
