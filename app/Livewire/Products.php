<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Products extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $categoryFilter = 'all';

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    #[Rule('required|min:2')]
    public string $name = '';

    public string $category = '';

    #[Rule('required|numeric|min:0')]
    public $price = '';

    // Image upload (new file) and existing image URL (for edit mode)
    #[Rule('nullable|image|max:2048')]
    public $image = null;

    public ?string $existing_image_url = null;

    #[Rule('nullable|max:500')]
    public string $description = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->image = null;

        // Get first category slug as default
        $defaultCategory = Category::first()?->slug ?? '';

        if ($id) {
            $product = Product::find($id);
            if ($product) {
                $this->editingId = $id;
                $this->name = $product->name;
                $this->category = $product->category;
                $this->price = $product->price;
                $this->existing_image_url = $product->image_url;
                $this->description = $product->description ?? '';
            }
        } else {
            $this->editingId = null;
            $this->name = '';
            $this->category = $defaultCategory;
            $this->price = '';
            $this->existing_image_url = null;
            $this->description = '';
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
        // Get valid category slugs from database
        $validCategories = Category::pluck('slug')->implode(',');

        $this->validate([
            'name' => 'required|min:2',
            'category' => 'required|in:' . $validCategories,
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|max:500',
        ]);

        // Handle image upload - store relative path only
        $imageUrl = $this->existing_image_url;
        if ($this->image) {
            $path = $this->image->store('products', 'public');
            // Store only the relative path (e.g., 'products/xxx.jpg')
            $imageUrl = $path;
        }

        // Look up category_id from the selected category slug
        $categoryModel = Category::where('slug', $this->category)->first();

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'category_id' => $categoryModel?->id,
            'price' => $this->price,
            'image_url' => $imageUrl,
            'description' => $this->description ?: null,
        ];

        $isEditing = (bool) $this->editingId;
        $productName = $this->name;

        if ($this->editingId) {
            Product::find($this->editingId)->update($data);
        } else {
            Product::create($data);
        }

        $this->closeModal();

        // Dispatch event for success notification
        $this->dispatch('product-saved', [
            'type' => $isEditing ? 'updated' : 'created',
            'name' => $productName,
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
            // Capture product name before deleting
            $product = Product::find($this->deletingId);
            $productName = $product?->name ?? '';
            
            // Delete the product
            Product::destroy($this->deletingId);
            
            // Dispatch event for category count refresh and notification
            $this->dispatch('product-saved', [
                'type' => 'deleted',
                'name' => $productName,
            ]);
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter !== 'all', fn($q) => $q->where('category', $this->categoryFilter))
            ->orderBy('name')
            ->paginate(10);

        // Load categories from database
        $categories = Category::orderBy('name')->get();

        return view('livewire.products', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
