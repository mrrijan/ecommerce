<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CheckoutPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;

Route::get("/", \App\Livewire\HomePage::class);
Route::get("/categories",\App\Livewire\CategoriesPage::class);
Route::get("/products",\App\Livewire\ProductsPage::class);
Route::get("/cart",CartPage::class);
Route::get("/products/{slug}",\App\Livewire\ProductDetailPage::class);





Route::middleware(['guest'])->group(function () {
//logins
    Route::get("/login",\App\Livewire\Auth\Login::class)->name('login');
    Route::get("/register",RegisterPage::class);
    Route::get("/forgot-password",ForgotPasswordPage::class);
    Route::get("/reset-password",ResetPasswordPage::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', function (){
        auth()->logout();
        return redirect("/");
    });
    Route::get("/checkout",CheckoutPage::class);
    Route::get("/my-orders",MyOrdersPage::class);
    Route::get("/my-orders/{order}",\App\Livewire\MyOrderDetailPage::class);
    Route::get("/success",SuccessPage::class);
    Route::get("/cancel",CancelPage::class);
});
