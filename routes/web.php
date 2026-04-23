<?php

use App\Http\Controllers\CardGeneratorController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\VerifikasiController;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\InputManual;
use App\Livewire\Admin\ManajemenAdmin;
use App\Livewire\Admin\ManajemenAnggota;
use App\Livewire\Admin\ManajemenKantor;
use App\Livewire\Admin\ManajemenPengurus;
use App\Livewire\Admin\RiwayatAktivitas;
use App\Livewire\Admin\StrukturOrganisasi;
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

Route::view('/', 'landing')->name('landing');
Route::get('/pendaftaran', PendaftaranAnggota::class)->name('pendaftaran');
Route::get('/login', Login::class)->name('login');
Route::get('/lupa-password', LupaPassword::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
Route::get('/verifikasi/{nomor}', [VerifikasiController::class, 'show'])->name('verify');

Route::get('/private-file', [FileController::class, 'servePrivateFile'])->name('file.private');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::middleware(['auth', 'role:anggota'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/profil', Profil::class)->name('profil');
    Route::get('/password', UbahPassword::class)->name('password');

    Route::middleware(['anggota.approved'])->group(function () {
        Route::get('/kartu', KartuDigital::class)->name('kartu');
        Route::get('/kartu/pdf', [CardGeneratorController::class, 'downloadPdf'])->name('kartu.pdf');
        Route::get('/kartu/png', [CardGeneratorController::class, 'downloadPng'])->name('kartu.png');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/struktur-organisasi', StrukturOrganisasi::class)->name('struktur');
        Route::get('/manajemen-anggota', ManajemenAnggota::class)->name('manajemen');
        Route::get('/manajemen-kantor', ManajemenKantor::class)->name('kantor');
        Route::get('/manajemen-pengurus', ManajemenPengurus::class)->name('pengurus');
        Route::get('/manajemen-admin', ManajemenAdmin::class)->name('admin');
        Route::get('/input-manual', InputManual::class)->name('input-manual');
        Route::get('/riwayat-aktivitas', RiwayatAktivitas::class)->name('riwayat');
    });
});
