<?php

namespace App\Livewire;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Expenses extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $categoryFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    // Form
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;
    public string $date = '';
    public string $category = '';
    public string $description = '';
    public string $amount = '';

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'date' => 'required|date',
            'category' => 'required|in:bahan_baku,operasional,gaji,lainnya',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->reset(['date', 'category', 'description', 'amount', 'editingId', 'isEditing']);
        $this->date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $expense = Expense::findOrFail($id);
        $this->editingId = $expense->id;
        $this->isEditing = true;
        $this->date = $expense->date->format('Y-m-d');
        $this->category = $expense->category;
        $this->description = $expense->description;
        $this->amount = (string) $expense->amount;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'date' => $this->date,
            'category' => $this->category,
            'description' => $this->description,
            'amount' => $this->amount,
        ];

        if ($this->isEditing) {
            $expense = Expense::findOrFail($this->editingId);
            $expense->update($data);
        } else {
            $data['created_by'] = Auth::id();
            Expense::create($data);
        }

        $this->showModal = false;
        $this->reset(['date', 'category', 'description', 'amount', 'editingId', 'isEditing']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Expense::destroy($this->deletingId);
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'categoryFilter', 'dateFrom', 'dateTo']);
    }

    public function render()
    {
        $query = Expense::query()->with('creator');

        if ($this->search) {
            $query->where('description', 'like', "%{$this->search}%");
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('date', '<=', $this->dateTo);
        }

        $expenses = $query->orderByDesc('date')->orderByDesc('created_at')->paginate(15);

        return view('livewire.expenses', [
            'expenses' => $expenses,
            'categories' => Expense::CATEGORIES,
        ]);
    }
}
