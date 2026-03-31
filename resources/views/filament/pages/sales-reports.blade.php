<x-filament-panels::page>
    <div class="flex justify-end mb-4 print:hidden">
        <a href="{{ route('reports.print.financial', ['from' => $this->form->getState()['from_date'], 'to' => $this->form->getState()['to_date']]) }}"
            target="_blank">
            <x-filament::button color="primary" icon="heroicon-m-document-text" class="shadow-sm">
                طباعة التقرير المفلتر (PDF)
            </x-filament::button>
        </a>
    </div>

    <div
        class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6 transition-all">
        <form wire:submit.prevent="filter" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full overflow-hidden">
                {{ $this->form }}
            </div>
            <x-filament::button type="submit" icon="heroicon-m-funnel" class="mb-1">
                تحديث البيانات

            </x-filament::button>
        </form>
    </div>

    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                class="relative p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium ">إجمالي الاستيراد</p>
                        <p class="text-2xl font-black  mt-1 font-mono">
                            ${{ number_format($totals['imports'], 2) }}</p>
                    </div>
                    <div class="p-3 bg-danger-50 dark:bg-danger-900/20 rounded-xl  group-hover:scale-110 transition">
                        <x-heroicon-o-arrow-down-tray class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <div
                class="relative p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مبيعات الكاشير</p>
                        <p class="text-2xl font-black text-success-600 mt-1 font-mono">
                            ${{ number_format($totals['cashier'], 2) }}</p>
                    </div>
                    <div
                        class="p-3 bg-success-50 dark:bg-success-900/20 rounded-xl text-success-600 group-hover:scale-110 transition">
                        <x-heroicon-o-computer-desktop class="w-6 h-6" />
                    </div>
                </div>
            </div>


            <div
                class="relative p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مرتجعات الكاشير</p>
                        <p class="text-2xl font-black  mt-1 font-mono">
                            ${{ number_format($totals['cashier_return'], 2) }}</p>
                    </div>
                    <div class="p-3 rounded-xl group-hover:scale-110 transition">
                        <x-heroicon-o-arrow-uturn-left class="w-6 h-6" />
                    </div>
                </div>
            </div>


            <div
                class="relative p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي مبيعات الكاشير
                        </p>
                        <p class="text-2xl font-black text-blue-600 mt-1 font-mono">
                            ${{ number_format($totals['cashier_net'], 2) }}
                        </p>
                    </div>
                    <div
                        class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600 group-hover:scale-110 transition">
                        <x-heroicon-o-calculator class="w-6 h-6" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 opacity-20"></div>
            </div>

            <div
                class="relative p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">مبيعات الموقع الالكتروني</p>
                        <p class="text-2xl font-black  mt-1 font-mono">
                            ${{ number_format($totals['online'], 2) }}</p>
                    </div>
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl group-hover:scale-110 transition">
                        <x-heroicon-o-shopping-cart class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <div
                class="relative p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي تحويلات نقاط البيع</p>
                        <p class="text-2xl font-black text-amber-600 mt-1 font-mono">
                            ${{ number_format($totals['transfers_net'], 2) }}</p>
                    </div>
                    <div
                        class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600 group-hover:scale-110 transition">
                        <x-heroicon-o-arrows-right-left class="w-6 h-6" />
                    </div>
                </div>
            </div>



            <div
                class="relative p-6 bg-white mb-4 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium uppercase tracking-wider">صافي العمليات
                            (Profit/Loss)
                        </p>
                        <p class="text-2xl font-black mt-2 font-mono">${{ number_format($totals['net_profit'], 2) }}</p>
                    </div>
                    <div
                        class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600 group-hover:scale-110 transition">
                        <x-heroicon-o-arrows-right-left class="w-6 h-6" />
                    </div>
                </div>
            </div>



            <div
                class="relative p-6 bg-white mb-4 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider">إجمالي السيولة
                            (الخزينة)
                        </p>
                        <p class="text-2xl font-black mt-2 font-mono">${{ number_format($totals['treasure'], 2) }}</p>
                    </div>
                    <div
                        class="p-3 bg-amber-50  dark:bg-amber-900/20 rounded-xl text-amber-600 group-hover:scale-110 transition">
                        <x-heroicon-o-arrows-right-left class="w-6 h-6" />
                    </div>
                </div>
            </div>
        </div>


        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">تفصيل التدفقات المالية للفترة المحددة</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50/50  text-xs font-bold uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">البند المالي</th>
                            <th class="px-6 py-4 text-center">العمليات</th>
                            <th class="px-6 py-4">إجمالي القيمة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium">عمليات الاستيراد والمشتريات</td>
                            <td class="px-6 py-4 text-center font-mono">
                                {{ number_format($totals['count_imports'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold  font-mono">
                                ${{ number_format($totals['imports'], 2) }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-success-600">مبيعات الكاشير المباشرة</td>
                            <td class="px-6 py-4 text-center font-mono">
                                {{ number_format($totals['count_cashier'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold text-success-600 font-mono">
                                ${{ number_format($totals['cashier'], 2) }}</td>


                        </tr>

                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-danger-500">المرتجعات (منتجات مرتجعة)</td>
                            <td class="px-6 py-4 text-center font-mono">
                                {{ number_format($totals['count_returns'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold text-danger-500 font-mono">
                                ${{ number_format($totals['cashier_return'], 2) }}</td>
                        </tr>



                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium ">طلبات الموقع الإلكتروني</td>
                            <td class="px-6 py-4 text-center font-mono">
                                {{ number_format($totals['count_online'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold font-mono">
                                ${{ number_format($totals['online'], 2) }}</td>
                        </tr>
                        <tr
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition border-t-2 border-amber-100 dark:border-amber-900/30">
                            <td class="px-6 py-4 text-sm font-medium text-amber-600">إيداعات نقاط البيع</td>
                            <td class="px-6 py-4 text-center font-mono">
                                {{ number_format($totals['count_trans_in'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold text-amber-600 font-mono">
                                ${{ number_format($totals['transfers_in'], 2) }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-red-400">سحوبات نقاط البيع</td>
                            <td class="px-6 py-4 text-center font-mono">
                                {{ number_format($totals['count_trans_out'] ?? 0) }}</td>
                            <td class="px-6 py-4 font-bold text-red-400 font-mono">
                                ${{ number_format($totals['transfers_out'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
