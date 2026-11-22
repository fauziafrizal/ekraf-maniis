<?php

use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::controller(PublicSiteController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/profil', 'profile')->name('profile');
    Route::get('/maniis', 'maniis')->name('maniis');
    Route::get('/subsektor', 'subsectors')->name('subsectors');
    Route::get('/program', 'programs')->name('programs.index');
    Route::get('/program/{slug}', 'program')->name('programs.show');
    Route::get('/kegiatan', 'activities')->name('activities.index');
    Route::get('/kegiatan/{slug}', 'activity')->name('activities.show');
    Route::get('/kontak', 'contact')->name('contact');
    Route::get('/sitemap.xml', 'sitemap')->name('sitemap');
});
