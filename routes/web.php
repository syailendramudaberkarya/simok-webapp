<?php

use App\Http\Controllers\CardGeneratorController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\VerifikasiController;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\InputManual;
use App\Livewire\Admin\ManajemenAnggota;
use App\Livewire\Admin\ManajemenKantor;
use App\Livewire\Anggota\KartuDigital;
use App\Livewire\Anggota\Profil;
use App\Livewire\Anggota\UbahPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\LupaPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\PendaftaranAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Public ─────────────────────────────────────────────
Route::view('/', 'landing')->name('landing');
Route::get('/pendaftaran', PendaftaranAnggota::class)->name('pendaftaran');
Route::get('/login', Login::class)->name('login');
Route::get('/lupa-password', LupaPassword::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
Route::get('/verifikasi/{nomor}', [VerifikasiController::class, 'show'])->name('verify');

// Private File
Route::get('/private-file', [FileController::class, 'servePrivateFile'])->name('file.private');

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// ─── Anggota (auth + role:anggota) ──────────────────────
Route::middleware(['auth', 'role:anggota'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/profil', Profil::class)->name('profil');
    Route::get('/password', UbahPassword::class)->name('password');

    Route::middleware(['anggota.approved'])->group(function () {
        Route::get('/kartu', KartuDigital::class)->name('kartu');
        Route::get('/kartu/pdf', [CardGeneratorController::class, 'downloadPdf'])->name('kartu.pdf');
        Route::get('/kartu/png', [CardGeneratorController::class, 'downloadPng'])->name('kartu.png');
    });
});

// ─── Admin (auth + role:admin) ──────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    // Unified login is handled via /login for both admin and anggota

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/struktur-organisasi', \App\Livewire\Admin\StrukturOrganisasi::class)->name('struktur');
        Route::get('/manajemen-anggota', ManajemenAnggota::class)->name('manajemen');
        Route::get('/manajemen-kantor', ManajemenKantor::class)->name('kantor');
        Route::get('/manajemen-admin', \App\Livewire\Admin\ManajemenAdmin::class)->name('admin');
        Route::get('/input-manual', InputManual::class)->name('input-manual');
    });
});
