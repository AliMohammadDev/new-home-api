<x-filament-panels::page>
    <div class="flex justify-end mb-4 print:hidden">
        <a href="{{ route('reports.print.products', ['from' => $data['from_date'], 'to' => $data['to_date']]) }}"
            target="_blank">
            <x-filament::button color="info" icon="heroicon-m-printer" class="shadow-sm">
                طباعة تقرير المخزون (PDF)
            </x-filament::button>
        </a>
    </div>



    <x-filament::section class="rounded-2xl shadow-sm overflow-hidden border-gray-200 dark:border-gray-700">
        <form wire:submit.prevent="filter" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                {{ $this->form }}
            </div>
            <x-filament::button type="submit" icon="heroicon-m-funnel" class="rounded-xl px-6">
                تحديث البيانات
            </x-filament::button>
        </form>
    </x-filament::section>





    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-filament::section class="rounded-2xl transition-all hover:scale-[1.01]">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary-50 dark:bg-primary-950/30 rounded-xl">
                    <x-heroicon-o-home-modern class="w-7 h-7 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">المخزون الرئيسي (الحالي)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['main_stock'] ?? 0) }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section class="rounded-2xl transition-all hover:scale-[1.01]">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-info-50 dark:bg-info-950/30 rounded-xl">
                    <x-heroicon-o-building-storefront class="w-7 h-7 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مخزون المستودعات المصغرة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['sub_warehouses_stock'] ?? 0) }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section class="rounded-2xl transition-all hover:scale-[1.01]">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-warning-50 dark:bg-warning-950/30 rounded-xl">
                    <x-heroicon-o-arrow-down-tray class="w-7 h-7 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الوارد (خلال الفترة)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['total_imported'] ?? 0) }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section class="rounded-2xl transition-all hover:scale-[1.01]">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-success-50 dark:bg-success-950/30 rounded-xl">
                    <x-heroicon-o-shopping-cart class="w-7 h-7 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المواد المباعة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['sold_items'] ?? 0) }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section class="rounded-2xl transition-all hover:scale-[1.01]">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                    <x-heroicon-o-arrow-path class="w-7 h-7 text-gray-600 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المرتجعات</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['returned_items'] ?? 0) }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section class="rounded-2xl transition-all hover:scale-[1.01] border-r-4 border-r-danger-500">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-danger-50 dark:bg-danger-950/30 rounded-xl">
                    <x-heroicon-o-trash class="w-7 h-7 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مواد مهدورة/تالفة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totals['wasted_items'] ?? 0) }}</p>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
