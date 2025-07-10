<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    //

    public function index(Request $request){
        $title = $request->title;
        $filter = $request->input('filter', '');

        $books = Book::when(
            $title,
            fn($query, $title) => $query->title($title)
        );

        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
             default => $books->latest()->withAvgRating()->withReviewsCount()
        };

        // $books = $books->get();
        // $cacheKey = 'books:' . $filter . ':' . $title;
        // $books = cache()->remember($cacheKey, 3600, fn() => $books->get());
        $books = $books->get();

        return view('books.index', ['books' => $books]);
    }
    public function create(){}

    public function store(Request $request){}

         public function show(int $id)
    {
        //
        $cacheKey = 'book:' . $id;

        // $book = cache()->remember($cacheKey, 3600, fn() => $book->load([
        //     'reviews' => fn($query) => $query->latest()
        // ]));

        $book = cache()->remember(
            $cacheKey,
            3600,
            fn() =>
            Book::with([
                'reviews' => fn($query) => $query->latest()
            ])->withAvgRating()->withReviewsCount()->findOrFail($id)
        );

        return view('books.show', ['book' => $book]);
    }

    public function edit(String $id){}

    public function update(Request $request,String $id){}

    public function destroy(String $id){}


}
