<div>
    <section class="bg-white py-2 antialiased dark:bg-gray-900 md:py-4">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">Shopping Cart</h2>

            <div class="mt-4 sm:mt-6 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
                    <div class="space-y-4 sm:space-y-6">
                        @foreach ($cartItems as $productId => $item)
                            @php
                                $product = App\Models\Product::find($productId);
                            @endphp
                            <div
                                class="relative rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition-all duration-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600 sm:p-4 md:p-4">
                                <!-- Remove button with better hover effect -->
                                <button wire:click="removeItem('{{ $productId }}')" type="button"
                                    class="absolute right-2 top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white transition-colors duration-200 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 dark:bg-gray-700 dark:hover:bg-red-600 dark:focus:ring-gray-600">
                                    <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Remove item</span>
                                </button>

                                <div class="flex items-start gap-3 sm:gap-4 md:items-center">
                                    <!-- Product image with border and hover effect -->
                                    <a href="{{ route('products.show', $product) }}" class="shrink-0">
                                        <div
                                            class="relative overflow-hidden rounded-md border border-gray-200 transition-all duration-200 hover:border-primary-500 dark:border-gray-600">
                                            <img class="h-16 w-16 object-cover sm:h-20 sm:w-20 dark:hidden"
                                                src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                                            <img class="hidden h-16 w-16 object-cover sm:h-20 sm:w-20 dark:block"
                                                src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                                        </div>
                                    </a>

                                    <!-- Product details -->
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <a href="{{ route('products.show', $product) }}"
                                            class="block text-sm font-medium text-gray-900 hover:text-primary-600 hover:underline dark:text-white dark:hover:text-primary-400 sm:text-base">
                                            {{ $product->name }}
                                        </a>

                                        <!-- Product info badges -->
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span
                                                class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                {{ $product->barcode }}
                                            </span>
                                            <span
                                                class="rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Stok: {{ $product->stock }}
                                            </span>
                                        </div>

                                        <!-- Price per item -->
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ format_currency($item['price']) }} / item
                                        </p>
                                    </div>

                                    <!-- Quantity controls and total price -->
                                    <div class="flex flex-col items-end space-y-2">
                                        <!-- Total price -->
                                        <p class="hidden text-sm font-bold text-gray-900 dark:text-white sm:text-base">
                                            Rp. {{ number_format($item['price'] * $item['quantity']) }}
                                        </p>

                                        <!-- Quantity selector -->
                                        <div
                                            class="flex items-center rounded-md border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700">
                                            <button wire:click="decrementQuantity('{{ $productId }}')"
                                                type="button"
                                                class="h-8 w-8 rounded-l-md border-r border-gray-200 bg-gray-100 text-gray-600 transition-colors duration-200 hover:bg-gray-200 focus:outline-none focus:ring-1 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
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
                                                class="h-8 w-10 border-0 bg-transparent text-center text-xs font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white sm:text-sm"
                                                value="{{ $item['quantity'] }}" />

                                            <button wire:click="incrementQuantity('{{ $productId }}')"
                                                type="button"
                                                class="h-8 w-8 rounded-r-md border-l border-gray-200 bg-gray-100 text-gray-600 transition-colors duration-200 hover:bg-gray-200 focus:outline-none focus:ring-1 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                                <svg class="mx-auto h-3 w-3" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($cartItems) === 0)
                            <div
                                class="flex min-h-[300px] flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 text-center dark:border-gray-600 dark:bg-gray-800 sm:min-h-[400px]">
                                <div class="max-w-md">
                                    <div
                                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8 text-primary-500 dark:text-primary-300" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 text-lg font-bold text-gray-800 dark:text-white">Your cart is empty
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Looks like you haven't added any items to your cart yet
                                    </p>
                                    <button wire:navigate href="/products"
                                        class="mt-6 inline-flex items-center justify-center rounded-lg bg-primary-500 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Shop Now
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if (count($cartItems) > 0)
                    <div class="mx-auto mt-4 sm:mt-6 max-w-4xl flex-1 space-y-4 sm:space-y-6 lg:mt-0 lg:w-full">
                        <div
                            class="space-y-3 sm:space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                            <form wire:submit.prevent="applyPromoCode" class="space-y-3 sm:space-y-4">
                                <div>
                                    <label for="voucher"
                                        class="mb-1 sm:mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                        Do you have promo code?
                                    </label>
                                    <input wire:model="promoCode" type="text" id="voucher"
                                        @if ($appliedPromoCode) readonly @endif
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-xs sm:text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500 @if ($appliedPromoCode) bg-gray-100 cursor-not-allowed dark:bg-gray-600 @endif"
                                        placeholder="{{ $appliedPromoCode ? $promoCode : 'Enter promo code' }}" />
                                </div>

                                @if (!$appliedPromoCode)
                                    <button type="submit"
                                        class="flex w-full items-center justify-center rounded-lg bg-primary-500 px-4 py-2 text-xs sm:text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        Apply Code
                                    </button>
                                @endif
                            </form>
                        </div>

                        <div
                            class="space-y-3 sm:space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                            <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">Order summary</p>

                            <div class="space-y-3 sm:space-y-4">
                                <div class="space-y-1 sm:space-y-2">
                                    <dl class="flex items-center justify-between gap-4">
                                        <dt class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400">
                                            Subtotal
                                        </dt>
                                        <dd class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">
                                            {{ format_currency($this->subtotal) }}</dd>
                                    </dl>

                                    <dl class="flex items-center justify-between gap-4">
                                        <dt class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400">
                                            Savings</dt>
                                        <dd class="text-xs sm:text-sm font-medium text-green-600">
                                            -{{ format_currency($this->savings) }}</dd>
                                    </dl>
                                </div>

                                <dl
                                    class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                    <dd class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                                        {{ format_currency($this->total) }}</dd>
                                </dl>
                            </div>

                            <button wire:navigate href='/shopping-cart/checkout'
                                class="flex w-full items-center justify-center rounded-lg bg-primary-500 px-4 py-2 text-xs sm:text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proceed
                                to Checkout</button>

                            <div class="flex items-center justify-center gap-2">
                                <span class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400"> or
                                </span>
                                <a wire:navigate href="/products"
                                    class="inline-flex items-center gap-1 text-xs sm:text-sm font-medium text-primary-700 underline hover:no-underline dark:text-primary-500">
                                    Continue Shopping
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
