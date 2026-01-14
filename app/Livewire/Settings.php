<?php

namespace App\Livewire;

use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    #[Rule('required|min:2')]
    public string $restaurantName = 'Kasir App';
    
    #[Rule('nullable|max:255')]
    public string $restaurantAddress = '';
    
    #[Rule('required|numeric|min:0|max:100')]
    public float $taxPercentage = 10;

    public bool $saved = false;

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
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
