<x-filament-panels::page>
    {{ $this->form }}

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        <x-filament::section>
            <x-slot name="heading">Pending Tasks</x-slot>
            <div class="text-3xl font-bold text-warning-500">{{ $this->stats['pending'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Completed Tasks</x-slot>
            <div class="text-3xl font-bold text-success-500">{{ $this->stats['completed'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Failed Tasks</x-slot>
            <div class="text-3xl font-bold text-danger-500">{{ $this->stats['failed'] }}</div>
        </x-filament::section>
        
        <x-filament::section>
            <x-slot name="heading">Total Posts</x-slot>
            <div class="text-3xl font-bold text-primary-500">{{ $this->stats['posts'] }}</div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
