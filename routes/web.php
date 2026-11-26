<?php

use App\Http\Controllers\CreativeAdminController;
use App\Http\Controllers\EkrafAssistantController;
use App\Http\Controllers\EkrafCommunityController;
use App\Http\Controllers\EkrafOfficeController;
use App\Http\Controllers\EkrafQrController;
use App\Http\Controllers\EkrafResearchController;
use App\Http\Controllers\EkrafServiceDeskController;
use App\Http\Controllers\EkrafStudioController;
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
    Route::get('/hub', 'hub')->name('creative.hub');
    Route::get('/marketplace', 'marketplace')->name('creative.market');
    Route::get('/direktori', 'creativeDirectory')->name('creative.directory');
    Route::get('/akademi', 'academy')->name('creative.academy');
    Route::get('/ekraf-pelajar', 'students')->name('creative.students');
    Route::get('/data-center', 'dataCenter')->name('creative.data');
    Route::get('/data-center/statistik.csv', 'dataCsv')->name('creative.csv');
    Route::get('/peta', 'creativeMap')->name('creative.map');
    Route::get('/alat-usaha', 'toolkit')->name('creative.toolkit');
    Route::get('/ruang-saya', 'mySpace')->name('creative.space');
    Route::get('/kalender', 'creativeCalendar')->name('creative.calendar');
    Route::get('/kalender/{slug}.ics', 'calendarEvent')->name('creative.calendar.ics');
    Route::get('/bantuan', 'creativeHelp')->name('creative.help');
    Route::get('/layanan', 'services')->name('services');
    Route::get('/kontak', 'contact')->name('contact');
    Route::get('/sitemap.xml', 'sitemap')->name('sitemap');
});

Route::controller(CreativeAdminController::class)->group(function () {
    Route::get('/pengurus/login', 'loginForm')->name('login');
    Route::post('/pengurus/login', 'login')->middleware('throttle:15,1')->name('creative.login');
    Route::middleware('auth')->group(function () {
        Route::get('/pengurus', 'index')->name('creative.admin');
        Route::post('/pengurus/katalog', 'store')->name('creative.store');
        Route::patch('/pengurus/katalog/{listing}', 'update')->name('creative.update');
        Route::post('/pengurus/logout', 'logout')->name('creative.logout');
    });
});

Route::controller(EkrafCommunityController::class)->group(function () {
    Route::get('/komunitas', 'index')->name('community.index');
    Route::post('/komunitas', 'submit')->middleware('throttle:4,10')->name('community.submit');
    Route::get('/komunitas/foto/{post}', 'photo')->name('community.photo');
    Route::get('/katalog/{listing}', 'listing')->name('community.listing');
    Route::get('/katalog/{listing}/foto', 'listingPhoto')->name('community.listing-photo');
    Route::post('/katalog/{listing}/ulasan', 'review')->middleware('throttle:4,10')->name('community.review');
    Route::get('/survei', 'feedbackForm')->name('community.feedback');
    Route::post('/survei', 'feedback')->middleware('throttle:3,10')->name('community.feedback.submit');
    Route::get('/verifikasi/{token}', 'verify')->whereUuid('token')->name('community.verify');
    Route::get('/peta-umkm', 'map')->name('community.map');
    Route::get('/creative-challenge', 'learning')->name('community.learning');
    Route::get('/notifikasi', 'alerts')->name('community.alerts');
});
Route::get('/katalog/{listing}/qr', [EkrafQrController::class, 'listing'])->name('community.listing-qr');
Route::get('/verifikasi/{token}/qr', [EkrafQrController::class, 'credential'])->whereUuid('token')->name('community.credential-qr');
Route::middleware('auth')->prefix('pengurus/sekretariat')->controller(EkrafOfficeController::class)->group(function () {
    Route::get('/', 'index')->name('office.index');
    Route::post('/dokumen', 'document')->name('office.document');
    Route::get('/dokumen/{document}/unduh', 'download')->name('office.download');
    Route::get('/dokumen/{document}/cetak', 'printDocument')->name('office.print');
    Route::post('/terbitkan', 'issue')->name('office.issue');
    Route::post('/cabut/{credential}', 'revoke')->name('office.revoke');
    Route::post('/moderasi/{kind}/{id}', 'moderate')->whereNumber('id')->name('office.moderate');
    Route::get('/pratinjau/{post}', 'preview')->name('office.post-preview');
    Route::post('/voting', 'poll')->name('office.poll');
    Route::post('/voting/{poll}/pilih', 'vote')->name('office.vote');
    Route::post('/voting/{poll}/tutup', 'close')->name('office.close');
});

Route::get('/studio-kreatif', [EkrafStudioController::class, 'index'])->name('community.studio');
Route::post('/studio-kreatif', [EkrafStudioController::class, 'generate'])->middleware('throttle:8,1')->name('community.studio.generate');

Route::get('/klasifikasi-pembinaan', [EkrafResearchController::class, 'index'])->name('community.research');
Route::post('/klasifikasi-pembinaan', [EkrafResearchController::class, 'predict'])->middleware('throttle:8,1')->name('community.research.predict');

Route::post('/asisten-ekraf', EkrafAssistantController::class)->middleware('throttle:12,1')->name('creative.assistant');

Route::controller(EkrafServiceDeskController::class)->group(function () {
    Route::get('/layanan-digital', 'index')->name('desk.index');
    Route::post('/layanan-digital', 'submit')->middleware('throttle:3,10')->name('desk.submit');
    Route::post('/layanan-digital/lacak', 'track')->middleware('throttle:10,1')->name('desk.track');
    Route::middleware('auth')->group(function () {
        Route::get('/pengurus/antrean-layanan', 'queue')->name('desk.queue');
        Route::patch('/pengurus/antrean-layanan/{ticket}', 'update')->name('desk.update');
    });
});

Route::get('/musik-latar.mp3', function () {
    return response()->file(public_path('audio/musik-degung-sunda.mp3'), ['Content-Type' => 'audio/mpeg']);
})->name('site.music');
