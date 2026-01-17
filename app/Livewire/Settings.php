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
        }
    }

    public function save(): void
    {
        $this->validate();

        $settings = [
            'restaurant_name' => $this->restaurantName,
            'restaurant_address' => $this->restaurantAddress,
            'tax_percentage' => $this->taxPercentage,
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
        DB::statement('PRAGMA foreign_keys = OFF');
        
        TransactionDetail::truncate();
        Transaction::truncate();
        Product::truncate();
        Category::truncate();
        Expense::truncate();
        Logistic::truncate();
        
        DB::statement('PRAGMA foreign_keys = ON');

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

            DB::statement('PRAGMA foreign_keys = OFF');
            
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
                    Transaction::create($item);
                }
            }

            if (!empty($backup['data']['transaction_details'])) {
                foreach ($backup['data']['transaction_details'] as $item) {
                    TransactionDetail::create($item);
                }
            }

            if (!empty($backup['data']['expenses'])) {
                foreach ($backup['data']['expenses'] as $item) {
                    Expense::create($item);
                }
            }

            if (!empty($backup['data']['logistics'])) {
                foreach ($backup['data']['logistics'] as $item) {
                    Logistic::create($item);
                }
            }

            DB::statement('PRAGMA foreign_keys = ON');

            $this->restoreFile = null;
            $this->dispatch('data-restored');
        } catch (\Exception $e) {
            $this->dispatch('restore-error', message: 'Gagal restore data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings');
    }
}

