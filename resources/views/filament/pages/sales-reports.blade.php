<x-filament-panels::page>
    <div class="flex justify-end mb-4 print:hidden">
        <a href="{{ route('reports.print.financial', ['from' => $this->form->getState()['from_date'], 'to' => $this->form->getState()['to_date']]) }}"
            target="_blank">
            <x-filament::button color="info" icon="heroicon-m-document-text" class="shadow-sm">
                طباعة التقرير المفلتر (PDF)
            </x-filament::button>
        </a>
    </div>

    <x-filament::section class="rounded-2xl shadow-sm overflow-hidden border-gray-200 dark:border-gray-700">
        <form wire:submit.prevent="filter" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full overflow-hidden">
                {{ $this->form }}
            </div>
            <x-filament::button type="submit" icon="heroicon-m-funnel" class="mb-1">
                تحديث البيانات
            </x-filament::button>
        </form>
    </x-filament::section>


    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            <x-filament::section
                class="rounded-2xl transition-all hover:scale-[1.02] shadow-sm bg-emerald-50/5 dark:bg-emerald-900/10 border-emerald-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-600 dark:text-emerald-400 text-sm font-bold uppercase">الخزينة</p>
                        <p class="text-2xl font-black mt-1 font-mono text-emerald-700 dark:text-emerald-300">
                            ${{ number_format($totals['treasure'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900/40 rounded-xl text-emerald-600 transition">
                        <x-heroicon-o-currency-dollar class="w-6 h-6" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section
                class="rounded-2xl transition-all hover:scale-[1.02] border-r-4 border-r-danger-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الاستيراد</p>
                        <p class="text-2xl font-black mt-1 font-mono text-danger-600">
                            ${{ number_format($totals['imports'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-danger-50 dark:bg-danger-900/20 rounded-xl text-danger-600 transition">
                        <x-heroicon-o-arrow-down-tray class="w-6 h-6" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="rounded-2xl transition-all hover:scale-[1.02] shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مبيعات الكاشير</p>
                        <p class="text-2xl font-black text-success-600 mt-1 font-mono">
                            ${{ number_format($totals['cashier'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-success-50 dark:bg-success-900/20 rounded-xl text-success-600 transition">
                        <x-heroicon-o-computer-desktop class="w-6 h-6" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="rounded-2xl transition-all hover:scale-[1.02] shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مرتجعات الكاشير</p>
                        <p class="text-2xl font-black text-gray-700 dark:text-gray-200 mt-1 font-mono">
                            ${{ number_format($totals['cashier_return'], 2) }}
                        </p>
                    </div>
                    <div class="p-3  rounded-xl  transition">
                        <x-heroicon-o-arrow-uturn-left class="w-6 h-6" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section
                class="rounded-2xl transition-all hover:scale-[1.02] shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي مبيعات الكاشير</p>
                        <p class="text-2xl font-black text-blue-600 mt-1 font-mono">
                            ${{ number_format($totals['cashier_net'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600 transition">
                        <x-heroicon-o-calculator class="w-6 h-6" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 opacity-30"></div>
            </x-filament::section>

            <x-filament::section class="rounded-2xl transition-all hover:scale-[1.02] shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مبيعات الموقع</p>
                        <p class="text-2xl font-black  mt-1 font-mono">
                            ${{ number_format($totals['online'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 rounded-xl transition">
                        <x-heroicon-o-shopping-cart class="w-6 h-6" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="rounded-2xl transition-all hover:scale-[1.02] shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي تحويلات POS</p>
                        <p class="text-2xl font-black text-amber-600 mt-1 font-mono">
                            ${{ number_format($totals['transfers_net'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600 transition">
                        <x-heroicon-o-arrows-right-left class="w-6 h-6" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section
                class="rounded-2xl transition-all hover:scale-[1.02] shadow-sm bg-indigo-50/5 dark:bg-indigo-900/10 border-indigo-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-600 dark:text-indigo-400 text-sm font-bold uppercase">الربح / الخسارة</p>
                        <p class="text-2xl font-black mt-1 font-mono text-indigo-700 dark:text-indigo-300">
                            ${{ number_format($totals['net_profit'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl text-indigo-600 transition">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                    </div>
                </div>
            </x-filament::section>


        </div>

        <x-filament::section class="rounded-2xl shadow-sm overflow-hidden" padding="none">
            <x-slot name="heading">
                تفصيل التدفقات المالية للفترة المحددة
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-right divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class=" dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-gray-500">البند
                                المالي</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-widest text-gray-500">
                                العمليات</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-gray-500">إجمالي
                                القيمة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium">عمليات الاستيراد والمشتريات</td>
                            <td class="px-6 py-4 text-center font-mono text-gray-600 dark:text-gray-400">
                                {{ number_format($totals['count_imports'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold font-mono text-danger-600">
                                -${{ number_format($totals['imports'], 2) }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium">مبيعات الكاشير المباشرة</td>
                            <td class="px-6 py-4 text-center font-mono text-gray-600 dark:text-gray-400">
                                {{ number_format($totals['count_cashier'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold text-success-600 font-mono">
                                +${{ number_format($totals['cashier'], 2) }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-danger-500">المرتجعات من الكاشير</td>
                            <td class="px-6 py-4 text-center font-mono text-gray-600 dark:text-gray-400">
                                {{ number_format($totals['count_returns'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold text-danger-500 font-mono">
                                -${{ number_format($totals['cashier_return'], 2) }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium">طلبات الموقع الإلكتروني</td>
                            <td class="px-6 py-4 text-center font-mono text-gray-600 dark:text-gray-400">
                                {{ number_format($totals['count_online'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold font-mono ">
                                +${{ number_format($totals['online'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>




</x-filament-panels::page>
