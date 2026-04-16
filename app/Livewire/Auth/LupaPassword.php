<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class LupaPassword extends Component
{
    public string $email = '';
    public bool $emailSent = false;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->emailSent = true;
            session()->flash('status', 'Link reset password telah dikirim ke email Anda.');
        } else {
            $this->addError('email', 'Email tidak ditemukan dalam sistem kami.');
        }
    }

    public function render()
    {
        return view('livewire.auth.lupa-password')
            ->layout('components.layouts.guest', ['title' => 'Lupa Password - SiMOK']);
    }
}
