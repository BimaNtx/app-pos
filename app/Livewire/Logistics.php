<?php

namespace App\Livewire;

use App\Models\Logistic;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Logistics extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;
    public $showDeleteModal = false;
    public $deletingId = null;

    // Form Fields
    #[Rule('required|min:3')]
    public $name = '';

    #[Rule('required')]
    public $unit = '';

    #[Rule('required|integer|min:0')]
    public $stock = 0;

    #[Rule('required|integer|min:0')]
    public $minimum_stock = 10;

    public $selected_id;

    public function render()
    {
        $logistics = Logistic::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.logistics', [
            'logistics' => $logistics
        ]);
    }

    public function create()
    {
        $this->resetInput();
        $this->openModal();
        $this->isEditMode = false;
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->name = '';
        $this->unit = '';
        $this->stock = 0;
        $this->minimum_stock = 10;
        $this->selected_id = null;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        // Check for duplicate name (case-insensitive)
        $existingItem = Logistic::whereRaw('LOWER(name) = ?', [strtolower($this->name)])
            ->when($this->selected_id, fn($q) => $q->where('id', '!=', $this->selected_id))
            ->first();

        if ($existingItem) {
            $this->dispatch('show-duplicate-error', [
                'message' => 'Barang ini sudah tersedia!',
            ]);
            return;
        }

        Logistic::updateOrCreate(['id' => $this->selected_id], [
            'name' => $this->name,
            'unit' => $this->unit,
            'stock' => $this->stock,
            'minimum_stock' => $this->minimum_stock,
        ]);

        $this->closeModal();
        session()->flash('message', $this->selected_id ? 'Item updated successfully.' : 'Item created successfully.');
    }

    public function edit($id)
    {
        $item = Logistic::findOrFail($id);
        $this->selected_id = $id;
        $this->name = $item->name;
        $this->unit = $item->unit;
        $this->stock = $item->stock;
        $this->minimum_stock = $item->minimum_stock;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function delete()
    {
        if ($this->deletingId) {
            Logistic::find($this->deletingId)->delete();
            session()->flash('message', 'Item berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }
}
