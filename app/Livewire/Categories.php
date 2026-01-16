<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Categories extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    #[Rule('required|min:2|max:50')]
    public string $name = '';

    #[Rule('required')]
    public string $icon = '🏷️';

    #[Rule('required')]
    public string $selectedColor = 'text-gray-700';

    #[Rule('required')]
    public string $selectedBg = 'bg-gray-100';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();

        if ($id) {
            $category = Category::find($id);
            if ($category) {
                $this->editingId = $id;
                $this->name = $category->name;
                $this->icon = $category->icon;
                $this->selectedColor = $category->color;
                $this->selectedBg = $category->bg_color;
            }
        } else {
            $this->editingId = null;
            $this->name = '';
            $this->icon = '🏷️';
            $this->selectedColor = 'text-gray-700';
            $this->selectedBg = 'bg-gray-100';
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
        $this->validate();

        $data = [
            'name' => $this->name,
            'icon' => $this->icon,
            'color' => $this->selectedColor,
            'bg_color' => $this->selectedBg,
        ];

        $isEditing = (bool) $this->editingId;
        $categoryName = $this->name;

        if ($this->editingId) {
            Category::find($this->editingId)->update($data);
        } else {
            Category::create($data);
        }

        $this->closeModal();

        // Dispatch event for success notification
        $this->dispatch('category-saved', [
            'type' => $isEditing ? 'updated' : 'created',
            'name' => $categoryName,
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $category = Category::find($this->deletingId);
            if ($category) {
                // Check if category has products
                if ($category->products()->count() > 0) {
                    $this->dispatch('category-delete-error', [
                        'message' => 'Tidak dapat menghapus kategori yang masih memiliki produk.',
                    ]);
                } else {
                    $category->delete();
                    $this->dispatch('category-deleted');
                }
            }
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function selectColor(string $color, string $bg): void
    {
        $this->selectedColor = $color;
        $this->selectedBg = $bg;
    }

    public function selectIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->withCount('products')
            ->orderBy('name')
            ->paginate(12);

        $totalProducts = Category::withCount('products')->get()->sum('products_count');

        return view('livewire.categories', [
            'categories' => $categories,
            'totalProducts' => $totalProducts,
            'colorOptions' => Category::colorOptions(),
            'iconOptions' => Category::iconOptions(),
        ]);
    }
}
