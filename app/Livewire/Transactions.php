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

    // Discount Settings
    public int $discountMinItems = 1;
    public float $discountMinTotal = 0;
    public string $discountConditionMode = 'total';

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

            // Load discount settings
            $settingsPath = storage_path('app/settings.json');
            if (File::exists($settingsPath)) {
                $settings = json_decode(File::get($settingsPath), true);
                if ($settings['discount_enabled'] ?? true) {
                    $this->discountMinItems = $settings['discount_min_items'] ?? 1;
                    $this->discountMinTotal = $settings['discount_min_total'] ?? 0;
                    $this->discountConditionMode = $settings['discount_condition_mode'] ?? 'total';
                }
            }

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

        // Calculate discount
        $discountAmount = $this->calculateEditDiscount($subtotal, collect($this->editItems)->sum('quantity'));

        // Apply tax percentage from settings (or original transaction tax percentage if preferred, 
        // but typically tax tracks current settings or transaction snapshot. sticking to current logic of settings tax)
        // Actually, updateTransaction uses selectedTransaction->tax_percentage ?? $this->taxPercentage. 
        // Let's use the transaction's tax percentage if available for consistency.
        $taxPercentage = $this->selectedTransaction->tax_percentage ?? $this->taxPercentage;
        
        $taxAmount = ($subtotal - $discountAmount) * ($taxPercentage / 100);

        return ($subtotal - $discountAmount) + $taxAmount;
    }

    public function getEditDiscountAmountProperty(): float
    {
        $subtotal = collect($this->editItems)->sum(function ($item) {
            return $item['quantity'] * $item['price_at_time'];
        });
        return $this->calculateEditDiscount($subtotal, collect($this->editItems)->sum('quantity'));
    }

    private function calculateEditDiscount(float $subtotal, int $totalQty): float
    {
        // 1. Check conditions
        if ($this->discountConditionMode === 'quantity') {
            if ($totalQty < $this->discountMinItems) {
                return 0;
            }
        } else {
            if ($subtotal < $this->discountMinTotal) {
                return 0;
            }
        }

        // 2. Calculate based on transaction's original discount value/type
        // If the transaction didn't have a discount type/value initially, we probably shouldn't invent one unless we fetch default settings?
        // However, usually "Edit" means re-evaluating the SAME discount rules on modified items.
        // If the original transaction had NO discount type, maybe we assume it was 0?
        // Or should we use the settings default discount? 
        // The user request says "kita sudah set diskon... saat edit... diskon error". 
        // This implies preserving the discount RULE (percentage/value) but re-checking conditions.
        
        $discountValue = $this->selectedTransaction->discount_value ?? 0;
        $discountType = $this->selectedTransaction->discount_type ?? 'percentage';

        if ($discountValue <= 0) return 0;

        if ($discountType === 'percentage') {
            $percentage = min(100, $discountValue);
            return $subtotal * ($percentage / 100);
        } else {
            return min($discountValue, $subtotal);
        }
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
        // Recalculate subtotal, tax, and total
        $subtotal = $this->selectedTransaction->details()->get()->sum(function ($detail) {
            return $detail->quantity * $detail->price_at_time;
        });

        $totalQty = $this->selectedTransaction->details()->get()->sum('quantity');
        $discountAmount = $this->calculateEditDiscount($subtotal, $totalQty);

        // Use the tax_percentage stored in the transaction (at time of sale)
        $taxPercentage = $this->selectedTransaction->tax_percentage ?? $this->taxPercentage;
        // Tax is usually applied after discount
        $taxableAmount = max(0, $subtotal - $discountAmount);
        $taxAmount = $taxableAmount * ($taxPercentage / 100);

        $this->selectedTransaction->update([
            'discount_amount' => $discountAmount, // Update discount amount
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal - $discountAmount + $taxAmount,
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
