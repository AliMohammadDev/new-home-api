<x-filament-panels::page>
    <div class="flex justify-end mb-4 print:hidden">
        <a href="{{ route('reports.print.products', ['from' => $data['from_date'], 'to' => $data['to_date']]) }}"
            target="_blank">
            <x-filament::button color="primary" icon="heroicon-m-printer" class="shadow-sm">
                طباعة تقرير المخزون (PDF)
            </x-filament::button>
        </a>
    </div>

    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 mb-6 shadow-sm">
        <form wire:submit.prevent="filter" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                {{ $this->form }}
            </div>
            <x-filament::button type="submit" icon="heroicon-m-funnel" class="mb-1">
                تحديث البيانات
            </x-filament::button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                    <x-heroicon-o-home-modern class="w-6 h-6 " />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">المخزون الرئيسي (النوع)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['main_stock']) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-info-50 dark:bg-info-900/20 rounded-lg">
                    <x-heroicon-o-building-storefront class="w-6 h-6 text-info-600" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">مخزون المستودعات المصغرة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['sub_warehouses_stock']) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-success-50 dark:bg-success-900/20 rounded-lg">
                    <x-heroicon-o-shopping-cart class="w-6 h-6 text-success-600" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المواد المباعة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['sold_items']) }}</p>
                </div>
            </div>
        </div>

        {{-- كرت: المهدورات --}}
        <div
            class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm border-r-4 border-r-danger-500">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-danger-50 dark:bg-danger-900/20 rounded-lg">
                    <x-heroicon-o-trash class="w-6 h-6 text-danger-600" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">مواد مهدورة/تالفة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['wasted_items']) }}</p>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
