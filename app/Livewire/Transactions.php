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
    public bool $showTrash = false;

    // Reprint state
    public ?int $reprintId = null;
    public ?Transaction $reprintTransaction = null;

    // CRUD Modal states
    public bool $showDetailModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    // Restore Modal state
    public bool $showRestoreModal = false;
    public ?int $restoringId = null;
    public ?Transaction $selectedTransaction = null;

    // Edit form data
    public string $editCustomerName = '';
    public array $editItems = [];

    // Batch selection
    public array $selectedIds = [];
    public bool $selectAll = false;
    public bool $showBatchDeleteModal = false;
    public bool $showBatchRestoreModal = false;

    // Tax Settings (loaded from settings.json)
    public float $taxPercentage = 10;

    public function updatingShowTrash(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    /**
     * Handle select all checkbox
     */
    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $query = Transaction::query();
            if ($this->showTrash) {
                $query->onlyTrashed();
            }
            $this->selectedIds = $query
                ->when($this->search, function ($q) {
                    $q->where('transaction_code', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                })
                ->when($this->dateFilter, function ($q) {
                    $q->whereDate('created_at', $this->dateFilter);
                })
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    /**
     * Toggle trash view
     */
    public function toggleTrash(): void
    {
        $this->showTrash = !$this->showTrash;
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    /**
     * Confirm restore transaction
     */
    public function confirmRestoreTransaction(int $id): void
    {
        $this->restoringId = $id;
        $this->showRestoreModal = true;
    }

    /**
     * Cancel restore
     */
    public function cancelRestore(): void
    {
        $this->showRestoreModal = false;
        $this->restoringId = null;
    }

    /**
     * Restore a soft-deleted transaction
     */
    public function restoreTransaction(): void
    {
        if ($this->restoringId) {
            $transaction = Transaction::onlyTrashed()->find($this->restoringId);
            if ($transaction) {
                $transaction->restore();
                session()->flash('message', 'Transaksi berhasil dipulihkan!');
            }
        }
        $this->showRestoreModal = false;
        $this->restoringId = null;
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

        // Recalculate subtotal, tax, and total
        $subtotal = $this->selectedTransaction->details()->get()->sum(function ($detail) {
            return $detail->quantity * $detail->price_at_time;
        });

        // Use the tax_percentage stored in the transaction (at time of sale)
        $taxPercentage = $this->selectedTransaction->tax_percentage ?? $this->taxPercentage;
        $taxAmount = $subtotal * ($taxPercentage / 100);

        $this->selectedTransaction->update([
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal + $taxAmount,
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

    /**
     * Confirm batch delete
     */
    public function confirmBatchDelete(): void
    {
        if (count($this->selectedIds) > 0) {
            $this->showBatchDeleteModal = true;
        }
    }

    /**
     * Batch delete transactions (soft delete)
     */
    public function batchDelete(): void
    {
        if (count($this->selectedIds) > 0) {
            $count = count($this->selectedIds);
            Transaction::whereIn('id', $this->selectedIds)->delete();

            session()->flash('message', $count . ' transaksi berhasil dihapus!');

            $this->selectedIds = [];
            $this->selectAll = false;
        }
        $this->showBatchDeleteModal = false;
    }

    /**
     * Cancel batch delete
     */
    public function cancelBatchDelete(): void
    {
        $this->showBatchDeleteModal = false;
    }

    /**
     * Confirm batch restore
     */
    public function confirmBatchRestore(): void
    {
        if (count($this->selectedIds) > 0) {
            $this->showBatchRestoreModal = true;
        }
    }

    /**
     * Batch restore transactions
     */
    public function batchRestore(): void
    {
        if (count($this->selectedIds) > 0) {
            $count = count($this->selectedIds);
            Transaction::onlyTrashed()->whereIn('id', $this->selectedIds)->restore();

            session()->flash('message', $count . ' transaksi berhasil dipulihkan!');

            $this->selectedIds = [];
            $this->selectAll = false;
        }
        $this->showBatchRestoreModal = false;
    }

    /**
     * Cancel batch restore
     */
    public function cancelBatchRestore(): void
    {
        $this->showBatchRestoreModal = false;
    }

    public function render()
    {
        // Load tax percentage from settings
        $settingsPath = storage_path('app/settings.json');
        if (File::exists($settingsPath)) {
            $settings = json_decode(File::get($settingsPath), true);
            $this->taxPercentage = $settings['tax_percentage'] ?? 10;
        }

        $query = Transaction::with('details.product');

        // Apply trash filter
        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        $transactions = $query
            ->when($this->search, function ($q) {
                $q->where('transaction_code', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateFilter, function ($q) {
                $q->whereDate('created_at', $this->dateFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.transactions', [
            'transactions' => $transactions,
            'taxPercentage' => $this->taxPercentage,
        ]);
    }
}
