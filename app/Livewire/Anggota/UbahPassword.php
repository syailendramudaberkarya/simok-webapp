<?php

namespace App\Livewire\Anggota;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class UbahPassword extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function rules()
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.regex' => 'Password baru harus mengandung huruf kapital dan angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    public function updatePassword()
    {
        $this->validate();

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_password',
            'description' => 'Memperbarui password sandi.',
            'ip_address' => request()->ip(),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('message', 'Password berhasil diubah!');
    }

    public function render()
    {
        return view('livewire.anggota.ubah-password')
            ->layout('components.layouts.app', ['title' => 'Ubah Password']);
    }
}
