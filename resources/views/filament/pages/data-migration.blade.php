<x-filament-panels::page>
    <div wire:loading wire:target="run_migration"
        class="fixed inset-0 z-9999 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm">
        <div
            class="bg-white dark:bg-gray-800 p-10 rounded-2xl shadow-2xl flex flex-col items-center border border-gray-200 dark:border-gray-700">
            <x-filament::loading-indicator class="h-16 w-16 text-warning-500 mb-6" />
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">جاري ترحيل البيانات...</h2>
            <p class="text-gray-500 dark:text-gray-400 text-center">يرجى عدم إغلاق المتصفح حتى انتهاء العملية.</p>
        </div>
    </div>

    <div class="space-y-6">
        <x-filament::section icon="heroicon-o-information-circle">
            <x-slot name="heading">سيتم ترحيل وأرشفة البنود التالية:</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $tables = [
                        'فواتير مرتجعات الكاشير',
                        'مبيعات الكاشير التفصيلية',
                        'فواتير مبيعات الكاشير',
                        'مرتجعات مبيعات الكاشير',
                        'مدخلات قيود الشركة',
                        'تحويلات مبيعات الشركة',
                        'الطلبات الخارجية',
                        'تحويلات نقاط البيع',
                        'شحنات المستودعات',
                        'مرتجعات المستودعات',
                        'بنود استيراد المنتجات',
                    ];
                @endphp

                @foreach ($tables as $table)
                    <div
                        class="flex items-center gap-2 p-3  dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-800">
                        <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5 text-success-500" />
                        <span class="text-sm font-medium">{{ $table }}</span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <div class="bg-danger-50 border-s-4 border-danger-400 p-4 rounded-md dark:bg-danger-900/20">
            <div class="flex">
                <div class="shrink-0">
                    <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-danger-400" />
                </div>
                <div class="ms-3">
                    <p class="text-sm text-danger-700 dark:text-danger-400 font-bold">
                        تنبيه: الترحيل الحالي سيقوم بأرشفة كافة البيانات المسجلة حتى هذه اللحظة. تأكد من إتمام كافة
                        العمليات المحاسبية قبل البدء.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
