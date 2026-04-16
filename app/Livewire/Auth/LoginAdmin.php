<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginAdmin extends Component
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

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'role' => 'admin'], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($throttleKey);
        $this->addError('email', 'Kredensial tidak valid untuk role Administrator.');
    }

    public function render()
    {
        return view('livewire.auth.login-admin')
            ->layout('components.layouts.guest', ['title' => 'Login Admin - SiMOK']);
    }
}
