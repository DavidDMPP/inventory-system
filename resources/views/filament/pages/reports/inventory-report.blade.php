{{-- resources/views/filament/pages/reports/inventory-report.blade.php --}}
<x-filament::page>
    <div class="space-y-6">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h2 class="text-lg font-medium mb-4 dark:text-white">{{ $this->getTitle() }}</h2>
            {{ $this->form }}
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            {{ $this->table }}
        </div>
    </div>
</x-filament::page>