<x-filament::page>
    <div class="flex gap-2 mb-6">
        <x-filament::button
            :color="$activeTab === 'customer' ? 'primary' : 'gray'"
            wire:click="setTab('customer')"
        >
            Customer Payments
        </x-filament::button>

        <x-filament::button
            :color="$activeTab === 'vendor' ? 'primary' : 'gray'"
            wire:click="setTab('vendor')"
        >
            Vendor Payments
        </x-filament::button>
    </div>

    {{ $this->table }}
</x-filament::page>
