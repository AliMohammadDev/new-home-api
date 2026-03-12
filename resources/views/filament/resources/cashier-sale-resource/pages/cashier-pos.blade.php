<x-filament-panels::page>
    <div class="space-y-6">

        <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
            <label for="barcode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">امسح الباركود
                هنا</label>
            <input type="text" id="barcode" wire:model="barcode" wire:keydown.enter="scanBarcode" autofocus
                class="bg-gray-50 border border-gray-300 text-gray-900 text-lg rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="|||||||||||||||">
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow dark:bg-gray-800">
            <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">المنتج</th>
                        <th scope="col" class="px-6 py-3">الكمية</th>
                        <th scope="col" class="px-6 py-3">السعر</th>
                        <th scope="col" class="px-6 py-3">الخصم</th>
                        <th scope="col" class="px-6 py-3">الإجمالي</th>
                        <th scope="col" class="px-6 py-3">حذف</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart as $index => $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item['name'] }} <br> <span class="text-xs text-gray-500">{{ $item['sku'] }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $item['quantity'] }}</td>
                            <td class="px-6 py-4">${{ number_format($item['price'], 2) }}</td>
                            <td class="px-6 py-4">${{ number_format($item['discount'], 2) }}</td>
                            <td class="px-6 py-4 font-bold">${{ number_format($item['total'], 2) }}</td>
                            <td class="px-6 py-4">
                                <button wire:click="removeItem({{ $index }})"
                                    class="text-red-600 hover:text-red-900">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">لم يتم إضافة أي منتج بعد. قم
                                بمسح الباركود للبدء.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between p-6 bg-gray-900 rounded-lg shadow text-white">
            <div>
                <span class="text-lg font-medium">المجموع الكلي للفاتورة:</span>
                <span class="block text-4xl font-bold text-green-400">${{ number_format($grandTotal, 2) }}</span>
            </div>

            <div class="flex gap-4">
                <x-filament::button tag="a" href="{{ url('/admin/cashier-sales/pos') }}" target="_blank"
                    color="primary" size="xl">
                    <span class="text-2xl font-bold px-4 py-2">فاتورة جديدة </span>
                </x-filament::button>

                <x-filament::button wire:click="checkout" color="success" size="xl">
                    <span class="text-2xl font-bold px-4 py-2">دفع </span>
                </x-filament::button>
            </div>
        </div>

    </div>
</x-filament-panels::page>
