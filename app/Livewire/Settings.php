<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Logistic;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Settings extends Component
{
    use WithFileUploads;

    #[Rule('required|min:2')]
    public string $restaurantName = 'Kasir App';
    
    #[Rule('nullable|max:255')]
    public string $restaurantAddress = '';
    
    #[Rule('required|numeric|min:0|max:100')]
    public float $taxPercentage = 10;

    #[Rule('required|numeric|min:0|max:100')]
    public float $discountPercentage = 0;

    #[Rule('required|numeric|min:1')]
    public int $discountMinItems = 1;

    #[Rule('required|numeric|min:0')]
    public float $discountMinTotal = 0;

    #[Rule('required|in:total,quantity')]
    public string $discountConditionMode = 'total';

    public bool $discountEnabled = true;

    public bool $saved = false;

    public $restoreFile;

    public function mount(): void
    {
        // Load settings from a simple JSON file
        $settingsPath = storage_path('app/settings.json');
        
        if (File::exists($settingsPath)) {
            $settings = json_decode(File::get($settingsPath), true);
            $this->restaurantName = $settings['restaurant_name'] ?? 'Kasir App';
            $this->restaurantAddress = $settings['restaurant_address'] ?? '';
            $this->taxPercentage = $settings['tax_percentage'] ?? 10;
            $this->discountPercentage = $settings['discount_percentage'] ?? 0;
            $this->discountMinItems = $settings['discount_min_items'] ?? 1;
            $this->discountMinTotal = $settings['discount_min_total'] ?? 0;
            $this->discountConditionMode = $settings['discount_condition_mode'] ?? 'total';
            $this->discountEnabled = $settings['discount_enabled'] ?? true;
        }
    }

    public function save(): void
    {
        $this->validate();

        $settings = [
            'restaurant_name' => $this->restaurantName,
            'restaurant_address' => $this->restaurantAddress,
            'tax_percentage' => $this->taxPercentage,
            'discount_percentage' => $this->discountPercentage,
            'discount_min_items' => $this->discountMinItems,
            'discount_min_total' => $this->discountMinTotal,
            'discount_condition_mode' => $this->discountConditionMode,
            'discount_enabled' => $this->discountEnabled,
        ];

        $settingsPath = storage_path('app/settings.json');
        File::ensureDirectoryExists(dirname($settingsPath));
        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));

        $this->saved = true;
        $this->dispatch('settings-saved');
    }

    public function clearCache(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        $this->dispatch('cache-cleared');
    }

    public function resetData(): void
    {
        $this->disableForeignKeyChecks();
        
        TransactionDetail::truncate();
        Transaction::truncate();
        Product::truncate();
        Category::truncate();
        Expense::truncate();
        Logistic::truncate();
        
        $this->enableForeignKeyChecks();

        $this->dispatch('data-reset');
    }

    public function downloadBackup()
    {
        $backup = [
            'created_at' => now()->toIso8601String(),
            'app_version' => '1.0',
            'data' => [
                'categories' => Category::all()->toArray(),
                'products' => Product::all()->toArray(),
                'transactions' => Transaction::all()->toArray(),
                'transaction_details' => TransactionDetail::all()->toArray(),
                'expenses' => Expense::all()->toArray(),
                'logistics' => Logistic::all()->toArray(),
            ]
        ];

        $filename = 'backup_toko_' . now()->format('Y-m-d_H-i') . '.json';
        $content = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function restoreData(): void
    {
        $this->validate([
            'restoreFile' => 'required|file|mimes:json|max:10240', // max 10MB
        ]);

        try {
            $content = file_get_contents($this->restoreFile->getRealPath());
            $backup = json_decode($content, true);

            if (!isset($backup['data'])) {
                $this->dispatch('restore-error', message: 'Format file backup tidak valid.');
                return;
            }

            $this->disableForeignKeyChecks();
            
            // Clear existing data
            TransactionDetail::truncate();
            Transaction::truncate();
            Product::truncate();
            Category::truncate();
            Expense::truncate();
            Logistic::truncate();

            // Restore data
            if (!empty($backup['data']['categories'])) {
                foreach ($backup['data']['categories'] as $item) {
                    Category::create($item);
                }
            }

            if (!empty($backup['data']['products'])) {
                foreach ($backup['data']['products'] as $item) {
                    Product::create($item);
                }
            }

            if (!empty($backup['data']['transactions'])) {
                foreach ($backup['data']['transactions'] as $item) {
                    // Parse timestamps strictly with proper format
                    $createdAt = isset($item['created_at']) 
                        ? \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i:s')
                        : now()->format('Y-m-d H:i:s');
                    $updatedAt = isset($item['updated_at']) 
                        ? \Carbon\Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s')
                        : now()->format('Y-m-d H:i:s');
                    
                    // Use DB insert for strict control
                    DB::table('transactions')->insert([
                        'id' => $item['id'] ?? null,
                        'customer_name' => $item['customer_name'] ?? 'Guest',
                        'transaction_code' => $item['transaction_code'] ?? Transaction::generateTransactionCode(),
                        'order_type' => $item['order_type'] ?? 'dine_in',
                        'table_number' => $item['table_number'] ?? null,
                        'payment_method' => $item['payment_method'] ?? 'cash',
                        'discount_type' => $item['discount_type'] ?? null,
                        'discount_value' => (float) ($item['discount_value'] ?? 0),
                        'discount_amount' => (float) ($item['discount_amount'] ?? 0),
                        'total_amount' => (float) ($item['total_amount'] ?? 0),
                        'amount_received' => (float) ($item['amount_received'] ?? 0),
                        'change_amount' => (float) ($item['change_amount'] ?? 0),
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ]);
                }
            }

            if (!empty($backup['data']['transaction_details'])) {
                foreach ($backup['data']['transaction_details'] as $item) {
                    TransactionDetail::create($item);
                }
            }

            if (!empty($backup['data']['expenses'])) {
                foreach ($backup['data']['expenses'] as $item) {
                    // Parse date strictly - handle ISO8601, Y-m-d, and other formats
                    $rawDate = $item['date'] ?? null;
                    if ($rawDate) {
                        try {
                            // Parse and convert to local timezone, then format as Y-m-d
                            $parsedDate = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $parsedDate = now()->format('Y-m-d');
                        }
                    } else {
                        $parsedDate = now()->format('Y-m-d');
                    }
                    
                    // Ensure amount is a positive numeric value (using abs to handle negative strings)
                    $rawAmount = $item['amount'] ?? 0;
                    $amount = abs((float) preg_replace('/[^0-9.\-]/', '', (string) $rawAmount));
                    
                    // Handle created_by - use current auth user if original user doesn't exist
                    $createdBy = $item['created_by'] ?? null;
                    if ($createdBy && !\App\Models\User::find($createdBy)) {
                        $createdBy = auth()->id() ?? 1;
                    }
                    $createdBy = $createdBy ?? auth()->id() ?? 1;
                    
                    // Parse timestamps
                    $createdAt = isset($item['created_at']) 
                        ? \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i:s')
                        : now()->format('Y-m-d H:i:s');
                    $updatedAt = isset($item['updated_at']) 
                        ? \Carbon\Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s')
                        : now()->format('Y-m-d H:i:s');

                    // Use DB insert for strict type control
                    DB::table('expenses')->insert([
                        'id' => $item['id'] ?? null,
                        'date' => $parsedDate,
                        'category' => $item['category'] ?? 'lainnya',
                        'description' => $item['description'] ?? '',
                        'amount' => $amount,
                        'created_by' => $createdBy,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ]);
                }
            }

            if (!empty($backup['data']['logistics'])) {
                foreach ($backup['data']['logistics'] as $item) {
                    Logistic::create($item);
                }
            }

            $this->enableForeignKeyChecks();

            $this->restoreFile = null;
            $this->dispatch('data-restored');
        } catch (\Exception $e) {
            $this->enableForeignKeyChecks();
            $this->dispatch('restore-error', message: 'Gagal restore data: ' . $e->getMessage());
        }
    }

    /**
     * Disable foreign key checks (database-agnostic)
     */
    protected function disableForeignKeyChecks(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    /**
     * Enable foreign key checks (database-agnostic)
     */
    protected function enableForeignKeyChecks(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    public function render()
    {
        return view('livewire.settings');
    }
}

