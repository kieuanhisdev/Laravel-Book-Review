<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    //

    public function index(Request $request){
        $title = $request->title;
        $books = Book::when(
            $title,
            fn($query, $title) => $query->title($title)
        )->get();

        return view('books.index', ['books' => $books]);
    }
    public function create(){}

    public function store(Request $request){}

    public function show(String $id){}

    public function edit(String $id){}

    public function update(Request $request,String $id){}

    public function destroy(String $id){}


}
