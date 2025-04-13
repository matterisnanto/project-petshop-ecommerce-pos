<div>
    <section class="bg-white py-4 antialiased dark:bg-gray-900 md:py-6">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">Your Shopping Cart</h2>
                <span
                    class="rounded-full bg-primary-100 px-3 py-1 text-sm font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300">
                    {{ count($cartItems) }} {{ count($cartItems) === 1 ? 'item' : 'items' }}
                </span>
            </div>

            <div class="mt-6 gap-6 lg:flex lg:items-start xl:gap-8">
                <!-- Cart Items Section -->
                <div class="mx-auto w-full flex-1 lg:max-w-2xl xl:max-w-4xl">
                    @if (count($cartItems) === 0)
                        <!-- Empty Cart State (tetap sama) -->
                    @else
                        <!-- Cart Items List -->
                        <div class="space-y-4">
                            @foreach ($cartItems as $productId => $item)
                                @php
                                    $product = App\Models\Product::find($productId);
                                @endphp
                                <div
                                    class="relative rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600 md:p-5">
                                    <!-- Remove button -->
                                    <button wire:click="removeItem('{{ $productId }}')" type="button"
                                        class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-gray-500 transition-colors duration-200 hover:bg-red-100 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-red-900 dark:hover:text-red-300">
                                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Remove item</span>
                                    </button>

                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                        <!-- Product image - made smaller on mobile -->
                                        <a href="{{ route('products.show', $product) }}" class="shrink-0">
                                            <div class="relative h-20 w-20 overflow-hidden rounded-lg sm:h-28 sm:w-28">
                                                <img class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                                                    src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                                                <div
                                                    class="absolute inset-0 rounded-lg ring-1 ring-inset ring-black/10">
                                                </div>
                                            </div>
                                        </a>

                                        <!-- Product details - adjusted spacing for mobile -->
                                        <div class="flex-1 min-w-0 space-y-1 sm:space-y-2">
                                            <div>
                                                <a href="{{ route('products.show', $product) }}"
                                                    class="text-sm font-medium text-gray-900 line-clamp-2 hover:text-primary-600 hover:underline dark:text-white dark:hover:text-primary-400 sm:text-base">
                                                    {{ $product->name }}
                                                </a>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    <span
                                                        class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                        {{ $product->barcode }}
                                                    </span>
                                                    <span
                                                        class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Stock: {{ $product->stock }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Product info badges - adjusted for mobile -->
                                            <div class="flex flex-wrap items-center gap-1 sm:gap-2">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                                                    {{ format_currency($item['price']) }} each
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Quantity controls and total price - stacked vertically on mobile -->
                                        <div
                                            class="flex w-full items-center justify-between sm:w-auto sm:flex-row sm:items-center sm:gap-4">

                                            <!-- Quantity selector -->
                                            <div
                                                class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700">
                                                <button wire:click="decrementQuantity('{{ $productId }}')"
                                                    type="button"
                                                    class="h-8 w-8 rounded-l-lg border-r border-gray-200 bg-gray-100 text-gray-600 transition-colors duration-200 hover:bg-gray-200 focus:outline-none focus:ring-1 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                                    <svg class="mx-auto h-3 w-3" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 18 2">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                    </svg>
                                                </button>

                                                <input type="text"
                                                    wire:model.lazy="cartItems.{{ $item['id'] }}.quantity"
                                                    wire:change="updateItemQuantity('{{ $productId }}', $event.target.value)"
                                                    class="h-8 w-12 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white"
                                                    value="{{ $item['quantity'] }}" />

                                                <button wire:click="incrementQuantity('{{ $productId }}')"
                                                    type="button"
                                                    class="h-8 w-8 rounded-r-lg border-l border-gray-200 bg-gray-100 text-gray-600 transition-colors duration-200 hover:bg-gray-200 focus:outline-none focus:ring-1 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                                    <svg class="mx-auto h-3 w-3" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 18 18">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M9 1v16M1 9h16" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Total price -->
                                            <p class="text-sm font-bold text-gray-900 dark:text-white sm:text-base">
                                                {{ format_currency($item['price'] * $item['quantity']) }}
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if (count($cartItems) > 0)
                    <!-- Order Summary Section - full width on mobile -->
                    <div class="mt-6 w-full space-y-6 lg:mt-0 lg:max-w-xs xl:max-w-md">
                        <!-- Promo Code Section -->
                        <div
                            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-5">
                            <h3 class="mb-3 text-lg font-medium text-gray-900 dark:text-white">Promo Code</h3>
                            <form wire:submit.prevent="applyPromoCode" class="space-y-3">
                                <div class="flex gap-2">
                                    <input wire:model="promoCode" type="text" id="voucher"
                                        @if ($appliedPromoCode) readonly @endif
                                        class="flex-1 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500 @if ($appliedPromoCode) bg-gray-100 cursor-not-allowed dark:bg-gray-600 @endif"
                                        placeholder="{{ $appliedPromoCode ? $promoCode : 'Enter promo code' }}" />

                                    @if (!$appliedPromoCode)
                                        <button type="submit"
                                            class="rounded-lg bg-primary-500 px-3 py-2.5 text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 sm:px-4">
                                            Apply
                                        </button>
                                    @else
                                        <button wire:click="removePromoCode" type="button"
                                            class="rounded-lg bg-red-500 px-3 py-2.5 text-sm font-medium text-white hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 sm:px-4">
                                            Remove
                                        </button>
                                    @endif
                                </div>

                                @error('promoCode')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </form>
                        </div>

                        <!-- Order Summary -->
                        <div
                            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-5">
                            <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white sm:mb-4">Order Summary
                            </h3>

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300 sm:text-base">Subtotal</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white sm:text-base">{{ format_currency($this->subtotal) }}</span>
                                </div>

                                @if ($this->savings > 0)
                                    <div class="flex justify-between">
                                        <span
                                            class="text-sm text-gray-600 dark:text-gray-300 sm:text-base">Discount</span>
                                        <span
                                            class="text-sm font-medium text-green-600 sm:text-base">-{{ format_currency($this->savings) }}</span>
                                    </div>
                                @endif

                                <div class="border-t border-gray-200 pt-3 dark:border-gray-700">
                                    <div class="flex justify-between">
                                        <span class="text-base font-semibold text-gray-900 dark:text-white">Total</span>
                                        <span
                                            class="text-base font-bold text-gray-900 dark:text-white">{{ format_currency($this->total) }}</span>
                                    </div>
                                </div>
                            </div>

                            <button wire:navigate href='/shopping-cart/checkout'
                                class="mt-4 flex w-full items-center justify-center rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 sm:mt-6 sm:py-3">
                                Proceed to Checkout
                            </button>

                            <div class="mt-3 flex items-center justify-center sm:mt-4">
                                <a wire:navigate href="/products"
                                    class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400">
                                    <svg class="mr-1 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                                    </svg>
                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
