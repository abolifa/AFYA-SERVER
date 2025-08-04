<x-filament::widget>
    <x-filament::card>
        <div wire:poll.1000ms class="text-center space-y-1">
            <h2 class="text-lg font-bold tracking-wider">🕒 الوقت الحالي</h2>
            <div class="text-2xl font-mono text-primary">{{ $time }}</div>
        </div>
    </x-filament::card>
</x-filament::widget>
