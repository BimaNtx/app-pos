<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public bool $showModal = false;
    public bool $nameSaved = false;
    public bool $passwordChanged = false;

    public function mount(): void
    {
        $this->name = Auth::user()->name ?? '';
    }

    #[On('open-profile-modal')]
    public function openModal(): void
    {
        $this->showModal = true;
        $this->resetMessages();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetMessages(): void
    {
        $this->nameSaved = false;
        $this->passwordChanged = false;
    }

    public function resetForm(): void
    {
        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->resetMessages();
        $this->resetValidation();
    }

    public function updateName(): void
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.min' => 'Nama minimal 2 karakter.',
        ]);

        $user = Auth::user();
        $user->name = $this->name;
        $user->save();

        $this->nameSaved = true;
        $this->passwordChanged = false;
        
        // Refresh page to update sidebar immediately
        $this->dispatch('profile-updated');
        $this->js('window.location.reload()');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:6',
            'newPasswordConfirmation' => 'required|same:newPassword',
        ], [
            'currentPassword.required' => 'Password lama wajib diisi.',
            'newPassword.required' => 'Password baru wajib diisi.',
            'newPassword.min' => 'Password baru minimal 6 karakter.',
            'newPasswordConfirmation.required' => 'Konfirmasi password wajib diisi.',
            'newPasswordConfirmation.same' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Password lama tidak sesuai.');
            return;
        }

        $user->password = $this->newPassword;
        $user->save();

        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->passwordChanged = true;
        $this->nameSaved = false;

        $this->dispatch('password-updated');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchAccount()
    {
        return $this->logout();
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
