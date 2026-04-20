<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::tabs label="Sales Filter">
            @foreach ($this->getTabs() as $key => $label)
                <x-filament::tabs.item :active="$this->activeTab === $key" wire:click="$set('activeTab', '{{ $key }}')">
                    {{ $label }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
