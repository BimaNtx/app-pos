<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\File;
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

    // CRUD Modal states
    public bool $showDetailModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public ?Transaction $selectedTransaction = null;

    // Edit form data
    public string $editCustomerName = '';
    public array $editItems = [];

    // Tax Settings (loaded from settings.json)
    public float $taxPercentage = 10;

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

    /**
     * View transaction detail in modal
     */
    public function viewDetail(int $id): void
    {
        $this->selectedTransaction = Transaction::with('details.product')->find($id);
        if ($this->selectedTransaction) {
            $this->showDetailModal = true;
        }
    }

    /**
     * Open edit modal for transaction
     */
    public function editTransaction(int $id): void
    {
        $this->selectedTransaction = Transaction::with('details.product')->find($id);
        if ($this->selectedTransaction) {
            $this->editCustomerName = $this->selectedTransaction->customer_name;
            $this->editItems = $this->selectedTransaction->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product->name ?? 'Produk tidak ditemukan',
                    'quantity' => $detail->quantity,
                    'price_at_time' => $detail->price_at_time,
                ];
            })->toArray();
            $this->showEditModal = true;
        }
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity(int $index, int $quantity): void
    {
        if ($quantity >= 1) {
            $this->editItems[$index]['quantity'] = $quantity;
        }
    }

    /**
     * Remove item from transaction (must have at least 1 item)
     */
    public function removeItem(int $index): void
    {
        if (count($this->editItems) > 1) {
            unset($this->editItems[$index]);
            $this->editItems = array_values($this->editItems);
        }
    }

    /**
     * Calculate edit total
     */
    public function getEditTotalProperty(): float
    {
        $subtotal = collect($this->editItems)->sum(function ($item) {
            return $item['quantity'] * $item['price_at_time'];
        });
        // Apply tax percentage from settings
        return $subtotal * (1 + $this->taxPercentage / 100);
    }

    /**
     * Save transaction changes
     */
    public function updateTransaction(): void
    {
        if (!$this->selectedTransaction) {
            return;
        }

        // Update customer name
        $this->selectedTransaction->update([
            'customer_name' => $this->editCustomerName,
        ]);

        // Get current detail IDs
        $existingIds = collect($this->editItems)->pluck('id')->filter()->toArray();

        // Delete removed items
        $this->selectedTransaction->details()
            ->whereNotIn('id', $existingIds)
            ->delete();

        // Update existing items
        foreach ($this->editItems as $item) {
            if (isset($item['id']) && $item['id']) {
                TransactionDetail::where('id', $item['id'])->update([
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        // Recalculate total with tax from settings
        $subtotal = $this->selectedTransaction->details()->get()->sum(function ($detail) {
            return $detail->quantity * $detail->price_at_time;
        });
        $this->selectedTransaction->update([
            'total_amount' => $subtotal * (1 + $this->taxPercentage / 100),
        ]);

        $this->closeModals();
        session()->flash('message', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Confirm delete transaction
     */
    public function confirmDeleteTransaction(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel delete
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    /**
     * Delete transaction
     */
    public function deleteTransaction(): void
    {
        if ($this->deletingId) {
            $transaction = Transaction::find($this->deletingId);
            if ($transaction) {
                // Soft delete - transaction details are preserved for auditing
                $transaction->delete();
                session()->flash('message', 'Transaksi berhasil dihapus (soft delete)!');
            }
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    /**
     * Close all modals
     */
    public function closeModals(): void
    {
        $this->showDetailModal = false;
        $this->showEditModal = false;
        $this->selectedTransaction = null;
        $this->editCustomerName = '';
        $this->editItems = [];
    }

    public function render()
    {
        // Load tax percentage from settings
        $settingsPath = storage_path('app/settings.json');
        if (File::exists($settingsPath)) {
            $settings = json_decode(File::get($settingsPath), true);
            $this->taxPercentage = $settings['tax_percentage'] ?? 10;
        }

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
            'taxPercentage' => $this->taxPercentage,
        ]);
    }
}
