<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Employees extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $positionFilter = 'all';
    public string $statusFilter = 'all';

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showResetPasswordModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $resettingPasswordId = null;

    // Form fields
    #[Rule('required|min:2|max:255')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('nullable|string|max:20')]
    public string $phone = '';

    #[Rule('required|in:admin,kasir,chef')]
    public string $position = 'kasir';

    #[Rule('nullable|image|max:2048')]
    public $avatar = null;

    public ?string $existing_avatar = null;

    public bool $is_admin = false;
    public bool $is_active = true;

    // Password fields (only for create)
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPositionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->avatar = null;
        $this->password = '';
        $this->password_confirmation = '';

        if ($id) {
            $user = User::find($id);
            if ($user) {
                $this->editingId = $id;
                $this->name = $user->name;
                $this->email = $user->email;
                $this->phone = $user->phone ?? '';
                $this->position = $user->position ?? 'kasir';
                $this->existing_avatar = $user->avatar;
                $this->is_admin = $user->is_admin ?? false;
                $this->is_active = $user->is_active ?? true;
            }
        } else {
            $this->editingId = null;
            $this->name = '';
            $this->email = '';
            $this->phone = '';
            $this->position = 'kasir';
            $this->existing_avatar = null;
            $this->is_admin = false;
            $this->is_active = true;
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingId,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|in:admin,kasir,chef',
            'avatar' => 'nullable|image|max:2048',
        ];

        // Password required only for new users
        if (!$this->editingId) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $this->validate($rules);

        // Handle avatar upload
        $avatarPath = $this->existing_avatar;
        if ($this->avatar) {
            $avatarPath = $this->avatar->store('avatars', 'public');
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'position' => $this->position,
            'avatar' => $avatarPath,
            'is_admin' => $this->is_admin,
            'is_active' => $this->is_active,
        ];

        $isEditing = (bool) $this->editingId;
        $employeeName = $this->name;

        if ($this->editingId) {
            User::find($this->editingId)->update($data);
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
        }

        $this->closeModal();

        // Dispatch event for success notification
        $this->dispatch('employee-saved', [
            'type' => $isEditing ? 'updated' : 'created',
            'name' => $employeeName,
        ]);
    }

    public function confirmDelete(int $id): void
    {
        // Prevent deleting yourself
        if ($id === Auth::id()) {
            $this->dispatch('employee-error', [
                'message' => 'Anda tidak dapat menghapus akun sendiri!',
            ]);
            return;
        }

        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId && $this->deletingId !== Auth::id()) {
            User::destroy($this->deletingId);
            $this->dispatch('employee-deleted');
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function confirmResetPassword(int $id): void
    {
        $this->resettingPasswordId = $id;
        $this->showResetPasswordModal = true;
    }

    public function resetPassword(): void
    {
        if ($this->resettingPasswordId) {
            $user = User::find($this->resettingPasswordId);
            if ($user) {
                $user->update([
                    'password' => Hash::make('password123'),
                ]);
                $this->dispatch('password-reset', [
                    'name' => $user->name,
                ]);
            }
        }
        $this->showResetPasswordModal = false;
        $this->resettingPasswordId = null;
    }

    public function cancelResetPassword(): void
    {
        $this->showResetPasswordModal = false;
        $this->resettingPasswordId = null;
    }

    public function toggleStatus(int $id): void
    {
        // Prevent disabling yourself
        if ($id === Auth::id()) {
            $this->dispatch('employee-error', [
                'message' => 'Anda tidak dapat menonaktifkan akun sendiri!',
            ]);
            return;
        }

        $user = User::find($id);
        if ($user) {
            $user->update([
                'is_active' => !$user->is_active,
            ]);
            $this->dispatch('status-toggled', [
                'name' => $user->name,
                'status' => $user->is_active ? 'aktif' : 'nonaktif',
            ]);
        }
    }

    public function render()
    {
        $employees = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%'))
            ->when($this->positionFilter !== 'all', fn($q) => $q->where('position', $this->positionFilter))
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('is_active', $this->statusFilter === 'active'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.employees', [
            'employees' => $employees,
        ]);
    }
}
