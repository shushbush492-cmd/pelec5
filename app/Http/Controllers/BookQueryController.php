<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

class BookQueryController extends Controller
{
    public function index(Request $request)
    {
        // Validate: whole number from 0 to 1000
        $request->validate([
            'stock_limit' => 'nullable|integer|min:0|max:1000',
        ]);

        $search = $request->input('search', '');
        $lowStock = $request->boolean('low_stock');

        // Use the typed number, or 10 if nothing was typed
        $stockLimit = $request->filled('stock_limit') ? (int) $request->input('stock_limit') : 10;

        // Filter stock if the checkbox is ticked OR a number was typed
        $useStock = $lowStock || $request->filled('stock_limit');

        // Laravel Query Builder
        $queryBuilderBooks = DB::table('books')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->when($useStock, function ($query) use ($stockLimit) {
                $query->where('stock', '<=', $stockLimit);
            })
            ->orderBy('amount')
            ->limit(20)
            ->get();

        // Eloquent ORM
        $eloquentBooks = Book::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->when($useStock, function ($query) use ($stockLimit) {
                $query->where('stock', '<=', $stockLimit);
            })
            ->orderBy('amount')
            ->limit(20)
            ->get();

        return view('pages.book-query-comparison', compact(
            'search', 'lowStock', 'stockLimit',
            'queryBuilderBooks', 'eloquentBooks'
        ));
    }
}