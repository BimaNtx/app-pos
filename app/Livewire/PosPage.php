<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.app')]
class PosPage extends Component
{
    // Search & Filter
    public string $search = '';
    public string $category = 'all';

    // Order Info
    public string $orderType = 'dine_in';

    #[Rule('required_if:orderType,dine_in', message: 'Table number is required for dine-in orders.')]
    public string $tableNumber = '';

    #[Rule('required', message: 'Customer name is required.')]
    public string $customerName = '';

    // Cart
    public array $cart = [];

    // Note Modal
    public bool $showNoteModal = false;
    public ?int $noteEditIndex = null;
    public string $itemNote = '';

    // Payment Modal
    public bool $showPaymentModal = false;
    public string $paymentMethod = 'cash';
    public $amountReceived = '';

    // Success State
    public bool $showSuccess = false;
    public ?string $lastTransactionCode = null;

    public function mount(): void
    {
        // Initialize with empty values
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->category !== 'all', fn($q) => $q->where('category', $this->category))
            ->get();
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function setOrderType(string $type): void
    {
        $this->orderType = $type;
        if ($type === 'takeaway') {
            $this->tableNumber = '';
        }
        // Clear validation errors when switching order type
        $this->resetValidation();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product)
            return;

        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] === $productId && empty($item['note'])) {
                $this->cart[$index]['quantity']++;
                return;
            }
        }

        $this->cart[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image_url' => $product->image_url,
            'quantity' => 1,
            'note' => '',
        ];
    }

    public function updateQuantity(int $index, int $change): void
    {
        if (!isset($this->cart[$index]))
            return;

        $newQty = $this->cart[$index]['quantity'] + $change;
        if ($newQty <= 0) {
            $this->removeFromCart($index);
        } else {
            $this->cart[$index]['quantity'] = $newQty;
        }
    }

    public function removeFromCart(int $index): void
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    // Note Modal
    public function openNoteModal(int $index): void
    {
        $this->noteEditIndex = $index;
        $this->itemNote = $this->cart[$index]['note'] ?? '';
        $this->showNoteModal = true;
    }

    public function saveNote(): void
    {
        if ($this->noteEditIndex !== null && isset($this->cart[$this->noteEditIndex])) {
            $this->cart[$this->noteEditIndex]['note'] = $this->itemNote;
        }
        $this->closeNoteModal();
    }

    public function closeNoteModal(): void
    {
        $this->showNoteModal = false;
        $this->noteEditIndex = null;
        $this->itemNote = '';
    }

    // Payment Modal
    public function openPaymentModal(): void
    {
        // Validate cart is not empty
        if (empty($this->cart))
            return;

        // Validate customer info based on order type
        $rules = [
            'customerName' => 'required|min:2',
        ];

        $messages = [
            'customerName.required' => 'Customer name is required.',
            'customerName.min' => 'Customer name must be at least 2 characters.',
        ];

        if ($this->orderType === 'dine_in') {
            $rules['tableNumber'] = 'required';
            $messages['tableNumber.required'] = 'Table number is required for dine-in orders.';
        }

        $this->validate($rules, $messages);

        // If validation passes, open payment modal
        $this->paymentMethod = 'cash';
        $this->amountReceived = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
    }

    public function setQuickCash(string $type): void
    {
        if ($type === 'exact') {
            $this->amountReceived = $this->total;
        } else {
            $this->amountReceived = (int) $type;
        }
    }

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    #[Computed]
    public function tax(): float
    {
        return $this->subtotal * 0.10;
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotal + $this->tax;
    }

    #[Computed]
    public function change(): float
    {
        $received = (float) ($this->amountReceived ?: 0);
        return max(0, $received - $this->total);
    }

    #[Computed]
    public function canPay(): bool
    {
        if ($this->paymentMethod !== 'cash')
            return true;
        return (float) ($this->amountReceived ?: 0) >= $this->total;
    }

    #[Computed]
    public function cartCount(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function confirmPayment(): void
    {
        if (empty($this->cart))
            return;
        if ($this->paymentMethod === 'cash' && !$this->canPay)
            return;

        DB::transaction(function () {
            $transaction = Transaction::create([
                'customer_name' => $this->customerName,
                'transaction_code' => Transaction::generateTransactionCode(),
                'order_type' => $this->orderType,
                'table_number' => $this->orderType === 'dine_in' ? $this->tableNumber : null,
                'payment_method' => $this->paymentMethod,
                'total_amount' => $this->total,
                'amount_received' => $this->paymentMethod === 'cash' ? $this->amountReceived : $this->total,
                'change_amount' => $this->paymentMethod === 'cash' ? $this->change : 0,
            ]);

            foreach ($this->cart as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $item['price'],
                    'note' => $item['note'] ?: null,
                ]);
            }

            $this->lastTransactionCode = $transaction->transaction_code;
        });

        $this->closePaymentModal();
        $this->showSuccess = true;
        $this->dispatch('checkout-success');
    }

    public function newOrder(): void
    {
        $this->cart = [];
        $this->customerName = '';
        $this->tableNumber = '';
        $this->orderType = 'dine_in';
        $this->showSuccess = false;
        $this->lastTransactionCode = null;
        $this->resetValidation();
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        return view('livewire.pos-page', [
            'categories' => $categories,
        ]);
    }
}
