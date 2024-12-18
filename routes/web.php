<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin() ? 
            redirect("/admin/dashboard") : 
            redirect("/user/dashboard");
    }
    return view("welcome");
});

Route::get("/register", function () {
    return redirect("/user/register");
})->name("register");

Route::get("/login", function () {
    return redirect("/user/login");
})->name("login");
