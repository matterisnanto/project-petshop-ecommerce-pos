<div class="grid grid-cols-1 dark:bg-gray-900 md:grid-cols-3 gap-4">
    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <div class="mb-4 flex gap-2">
            <input wire:model.live.debounce.300ms='search' type="text" placeholder="Search {{ ucfirst($activeTab) }}..."
                class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
            <x-filament::button x-data="" x-on:click="$dispatch('toggle-scanner')" color="primary">
                <x-lucide-scan-barcode class="w-5 h-5" />
            </x-filament::button>
            <livewire:scanner-modal-component />
        </div>

        <!-- Tabs for Products/Animals/Services -->
        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4 overflow-x-auto">
            <button wire:click="switchTab('products')"
                class="py-2 px-4 font-medium text-sm border-b-2 {{ $activeTab === 'products' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Products
            </button>
            <button wire:click="switchTab('animals')"
                class="py-2 px-4 font-medium text-sm border-b-2 {{ $activeTab === 'animals' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Animals
            </button>
            <button wire:click="switchTab('grooming')"
                class="py-2 px-4 font-medium text-sm border-b-2 {{ $activeTab === 'grooming' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Grooming
            </button>
            <button wire:click="switchTab('hotel')"
                class="py-2 px-4 font-medium text-sm border-b-2 {{ $activeTab === 'hotel' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Hotel
            </button>
            <button wire:click="switchTab('breeding')"
                class="py-2 px-4 font-medium text-sm border-b-2 {{ $activeTab === 'breeding' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Breeding
            </button>
        </div>

        <div class="flex-grow">
            <div class="grid grid-cols-8 sm:grid-cols-3 md:grid-cols-8 lg:grid-cols-4 gap-4">
                @if ($activeTab === 'products')
                    @foreach ($products as $item)
                        <div wire:click="addToOrder({{ $item->id }})"
                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow cursor-pointer">
                            <img src="{{ asset($item['thumbnail'] ? 'storage/' . $item['thumbnail'] : 'img/default.png') }}"
                                alt="Product Image" class="w-full h-32 object-cover rounded-md"
                                onerror="this.onerror=null; this.src='{{ asset('img/default.png') }}';">
                            <h3 class="text-sm font-semibold">{{ $item->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Rp.
                                {{ number_format($item->selling_price, 0, ',', '.') }}</p>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Stok: {{ $item->stock }}</p>
                        </div>
                    @endforeach
                @elseif ($activeTab === 'animals')
                    @foreach ($animals as $item)
                        <div wire:click="addToOrder({{ $item->id }})"
                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow cursor-pointer">
                            <img src="{{ asset($item['thumbnail'] ? 'storage/' . $item['thumbnail'] : 'img/default.png') }}"
                                alt="Animal Image" class="w-full h-32 object-cover rounded-md"
                                onerror="this.onerror=null; this.src='{{ asset('img/default.png') }}';">
                            <h3 class="text-sm font-semibold">{{ $item->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Rp.
                                {{ number_format($item->selling_price, 0, ',', '.') }}</p>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Stok: {{ $item->stock }}</p>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Jenis: {{ $item->breeds->name ?? '-' }}
                            </p>
                        </div>
                    @endforeach
                @elseif ($activeTab === 'grooming')
                    @foreach ($groomings as $item)
                        <div wire:click="addToOrder({{ $item->id }})"
                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow cursor-pointer">
                            <img src="{{ asset($item['photo'] ? 'storage/' . $item['photo'] : 'img/default.png') }}"
                                alt="Grooming Image" class="w-full h-32 object-cover rounded-md"
                                onerror="this.onerror=null; this.src='{{ asset('img/default.png') }}';">
                            <h3 class="text-sm font-semibold">{{ $item->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Rp.
                                {{ number_format($item->selling_price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                @elseif ($activeTab === 'hotel')
                    @foreach ($hotels as $item)
                        <div wire:click="addToOrder({{ $item->id }})"
                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow cursor-pointer">
                            <img src="{{ asset($item['thumbnail'] ? 'storage/' . $item['thumbnail'] : 'img/default.png') }}"
                                alt="Hotel Image" class="w-full h-32 object-cover rounded-md"
                                onerror="this.onerror=null; this.src='{{ asset('img/default.png') }}';">
                            <h3 class="text-sm font-semibold">{{ $item->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Rp.
                                {{ number_format($item->price_per_day, 0, ',', '.') }}/day</p>
                        </div>
                    @endforeach
                @elseif ($activeTab === 'breeding')
                    @foreach ($breedings as $item)
                        <div wire:click="addToOrder({{ $item->id }})"
                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow cursor-pointer">
                            <img src="{{ asset($item['photo'] ? 'storage/' . $item['photo'] : 'img/default.png') }}"
                                alt="Breeding Image" class="w-full h-32 object-cover rounded-md"
                                onerror="this.onerror=null; this.src='{{ asset('img/default.png') }}';">
                            <h3 class="text-sm font-semibold">{{ $item->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Rp.
                                {{ number_format($item->selling_price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="py-4">
                @if ($activeTab === 'products')
                    {{ $products->links() }}
                @elseif ($activeTab === 'animals')
                    {{ $animals->links() }}
                @elseif ($activeTab === 'grooming')
                    {{ $groomings->links() }}
                @elseif ($activeTab === 'hotel')
                    {{ $hotels->links() }}
                @elseif ($activeTab === 'breeding')
                    {{ $breedings->links() }}
                @endif
            </div>
        </div>
    </div>
    <div class="md:col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        @if (count($order_items) > 0)
            <div class="py-4">
                <h3 class="text-lg font-semibold text-center">Total: Rp. {{ number_format($total_price, 0, ',', '.') }}
                </h3>
            </div>
        @endif

        @foreach ($order_items as $key => $item)
            <div class="mb-4">
                <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <img src="{{ $item['thumbnail'] ? asset('storage/' . $item['thumbnail']) : asset('img/default.png') }}"
                            alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-md"
                            onerror="this.onerror=null; this.src='{{ asset('img/default.png') }}';">
                        <div class="px-2">
                            <h3 class="text-sm font-semibold">{{ $item['name'] }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Rp
                                {{ number_format($item['selling_price'], 0, ',', '.') }}</p>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">
                                @if ($item['type'] === 'product')
                                    Product
                                @elseif ($item['type'] === 'animal')
                                    Animal
                                @elseif ($item['type'] === 'grooming')
                                    Grooming
                                @elseif ($item['type'] === 'hotel')
                                    Hotel ({{ $item['quantity'] }} days)
                                @elseif ($item['type'] === 'breeding')
                                    Breeding
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <x-filament::button color="warning"
                            wire:click="decreaseQuantity({{ $key }})">-</x-filament::button>
                        <span class="px-4">{{ $item['quantity'] }}</span>
                        <x-filament::button color="success"
                            wire:click="increaseQuantity({{ $key }})">+</x-filament::button>
                    </div>
                </div>
            </div>
        @endforeach

        <form wire:submit="checkout">
            {{ $this->form }}
            <x-filament::button type="submit"
                class="w-full bg-red-500 mt-3 text-white py-2 rounded">Checkout</x-filament::button>
        </form>
    </div>
</div>

@script
    <script>
        // Handle download setelah checkout
        $wire.on('receipt-downloaded', (event) => {
            // Trigger download
            const link = document.createElement('a');
            link.href = event.url;
            link.download = event.filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
@endscript
<script src="https://unpkg.com/html5-qrcode"></script>
