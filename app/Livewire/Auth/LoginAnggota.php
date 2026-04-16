<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginAnggota extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function login()
    {
        $this->validate();

        $throttleKey = mb_strtolower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'role' => 'anggota'], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            return redirect()->intended(route('anggota.profil'));
        }

        RateLimiter::hit($throttleKey);
        $this->addError('email', 'Kredensial yang diberikan tidak cocok dengan data kami atau Anda bukan anggota.');
    }

    public function render()
    {
        return view('livewire.auth.login-anggota')
            ->layout('components.layouts.guest', ['title' => 'Login Anggota - SiMOK']);
    }
}
