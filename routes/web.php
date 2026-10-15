<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GymCustomerController;
use App\Http\Controllers\GymDashboardController;
use App\Http\Controllers\GymOrderController;
use App\Http\Controllers\GymProductController;
use App\Http\Controllers\BookQueryController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'adminLogin']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/customer-register', [AuthController::class, 'showCustomerRegister']);
Route::post('/customer-register', [AuthController::class, 'customerRegister']);

Route::middleware('admin.auth')->group(function () {
    Route::get('/person', function () {
        return view('person');
    });

    Route::get('/page1/{movie?}', function (?string $movie = '') {
        return view('pages.page1', compact('movie'));
    })->where('movie', '.*')->name('page1');

    //ws2.3 Dashboard
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    });

    Route::get('/p1/{movie?}', function (?string $movie = '') {
        return view('pages.page1', compact('movie'));
    })->where('movie', '.*')->name('p1');

    Route::get('/search_results.html', function () {
        $search = trim(request('top-search', ''));

        if ($search === '') {
            return redirect('/p2');
        }

        $data = DB::table('books')
            ->where('title', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->orWhere('country_id', 'like', '%' . $search . '%')
            ->get();

        return view('pages.page2', compact('data'));
    });

    Route::get('/p4', function () {
        return view('pages.page4');
    });



    //ws2.4 CRUD


    Route::get('/p3', [GymProductController::class, 'index']);
    Route::get('/gym-dashboard', [GymDashboardController::class, 'index']);
    Route::get('/gym-product-form', [GymProductController::class, 'show_add_form']);
    Route::post('/add-gym-product', [GymProductController::class, 'do_add']);
    Route::get('/edit-gym-product/{id}', [GymProductController::class, 'show_edit_form']);
    Route::post('/update-gym-product', [GymProductController::class, 'do_update']);
    Route::get('/delete-gym-product/{id}', [GymProductController::class, 'do_delete']);
    Route::get('/gym-customers', [GymCustomerController::class, 'index']);
    Route::put('/gym-customers/{id}', [GymCustomerController::class, 'update'])->name('gym-customers.update');
    Route::get('/gym-orders', [GymOrderController::class, 'index']);
    Route::get('/delete-gym-order/{id}', [GymOrderController::class, 'do_delete']);


    Route::get('/p2', [BookController::class, 'index']);
    Route::get('/book-form', [BookController::class, 'show_add_form']);
    Route::post('/add-book', [BookController::class, 'do_add']);
    Route::get('/edit-book/{id}', [BookController::class, 'show_edit_form']);
    Route::post('/update-book', [BookController::class, 'do_update']);
    Route::get('/delete-book/{id}', [BookController::class, 'do_delete']);

    Route::get('/book-query-comparison', [BookQueryController::class, 'index']);
});
