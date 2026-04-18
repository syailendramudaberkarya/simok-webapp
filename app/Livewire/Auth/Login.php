<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $login = '';
    public string $password = '';
    public bool $remember = false;

    public function rules()
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required'],
        ];
    }

    public function authenticate()
    {
        $this->validate();

        $throttleKey = mb_strtolower($this->login) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Determine if login is email or username
        $field = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $this->login, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            
            if (Auth::user()->isAdmin()) {
                $this->redirect(route('admin.dashboard'), navigate: true);
                return;
            }

            $this->redirect(route('anggota.profil'), navigate: true);
            return;
        }

        RateLimiter::hit($throttleKey);
        $this->addError('login', 'Kredensial yang diberikan tidak valid.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest', ['title' => 'Login - SiMOK']);
    }
}
