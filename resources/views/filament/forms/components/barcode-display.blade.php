<div
    class="flex flex-col items-center justify-center p-4 bg-white rounded-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    @if ($getRecord() && $getRecord()->barcode)
        <div class="mb-2">
            {!! \DNS1D::getBarcodeHTML((string) $getRecord()->barcode, 'C128', 1.5, 45) !!}
        </div>

        <div class="text-sm font-mono font-bold tracking-widest text-white-700 dark:text-white-300">
            {{ $getRecord()->barcode }}
        </div>
    @else
        <span class="text-white text-xs">لا يوجد باركود متاح</span>
    @endif
</div>
