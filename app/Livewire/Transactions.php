<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Transactions extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFilter = '';
    
    // Reprint state
    public ?int $reprintId = null;
    public ?Transaction $reprintTransaction = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function reprint(int $id): void
    {
        $this->reprintTransaction = Transaction::with('details.product')->find($id);
        if ($this->reprintTransaction) {
            $this->reprintId = $id;
            $this->dispatch('reprint-receipt');
        }
    }

    public function closeReprint(): void
    {
        $this->reprintId = null;
        $this->reprintTransaction = null;
    }

    public function render()
    {
        $transactions = Transaction::with('details.product')
            ->when($this->search, function ($query) {
                $query->where('transaction_code', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('created_at', $this->dateFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.transactions', [
            'transactions' => $transactions,
        ]);
    }
}
