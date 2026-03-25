@props(['id', 'title'])

<div x-data="{ open: false }" 
     @open-modal-{{ $id }}.window="open = true" 
     @close-modal.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[150] overflow-y-auto " 
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        {{-- Overlay Gelap --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

        {{-- Box Modal --}}
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $title }}</h3>
            
            <div class="text-sm text-gray-500 mb-6">
                {{ $slot }}
            </div>

            <div class="flex justify-end gap-3">
                {{ $footer }}
            </div>
        </div>
    </div>
</div>