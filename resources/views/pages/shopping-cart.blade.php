<div>
    <section class="bg-white py-2 antialiased dark:bg-gray-900 md:py-4">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Shopping Cart</h2>

            <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
                    <div class="space-y-6">
                        @foreach ($cartItems as $productId => $item)
                            @php
                                $product = App\Models\Product::find($productId);
                            @endphp
                            <div
                                class="relative rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                                <!-- Remove button (X) in top right corner -->
                                <button wire:click="removeItem('{{ $productId }}')  " type="button"
                                    class="absolute right-2 top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                    <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Remove item</span>
                                </button>

                                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                    <a href="{{ route('products.show', $product) }}" class="shrink-0 md:order-1">
                                        <img class="h-20 w-20 dark:hidden" src="{{ $product->image_url }}"
                                            alt="{{ $product->name }}" />
                                        <img class="hidden h-20 w-20 dark:block" src="{{ $product->image_url }}"
                                            alt="{{ $product->name }}" />
                                    </a>

                                    <div class="flex items-center justify-between md:order-3 md:justify-end">
                                        <div class="flex items-center">
                                            <button wire:click="decrementQuantity('{{ $productId }}')" type="button"
                                                class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text"
                                                wire:change="updateItemQuantity('{{ $productId }}', $event.target.value)"
                                                class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white"
                                                value="{{ $item['quantity'] }}" />
                                            <button wire:click="incrementQuantity('{{ $productId }}')" type="button"
                                                class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="text-end md:order-4 md:w-32">
                                            <p class="text-base font-bold text-gray-900 dark:text-white">
                                                Rp. {{ number_format($item['price'] * $item['quantity']) }}</p>
                                        </div>
                                    </div>

                                    <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                        <a href="{{ route('products.show', $product) }}"
                                            class="text-base font-medium text-gray-900 hover:underline dark:text-white">{{ $product->name }}</a>
                                        <p>{{ $product->barcode }}</p>
                                        <p>stok :{{ $product->stock }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($cartItems) === 0)
                            <div
                                class="sm:min-h-100 min-h-100 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900">
                                <div class="max-w-125 w-full bg-white dark:bg-gray-800 p-8 text-center">
                                    <!-- Icon -->
                                    <div
                                        class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 dark:bg-primary-900 mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8 text-primary-500 dark:text-primary-300" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>

                                    <!-- Message -->
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Your cart is empty
                                    </h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-6">Looks like you haven't added any
                                        items to your cart yet</p>

                                    <!-- Button -->
                                    <button wire:navigate href="/products"
                                        class="w-full flex items-center justify-center space-x-2 px-6 py-3 bg-primary-500 hover:bg-primary-600 focus:ring-4 focus:ring-blue-300 focus:outline-none rounded-lg text-white font-medium transition-all duration-200 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Shop Now</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if (count($cartItems) > 0)
                    <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full">
                        <div
                            class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                            <form wire:submit.prevent="applyPromoCode" class="space-y-4">
                                <div>
                                    <label for="voucher"
                                        class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                        Do you have promo code?
                                    </label>
                                    <input wire:model="promoCode" type="text" id="voucher"
                                        @if ($appliedPromoCode) readonly @endif
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500 @if ($appliedPromoCode) bg-gray-100 cursor-not-allowed dark:bg-gray-600 @endif"
                                        placeholder="Enter promo code" />
                                </div>

                                @if (!$appliedPromoCode)
                                    <button type="submit"
                                        class="flex w-full items-center justify-center rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        Apply Code
                                    </button>
                                @endif
                            </form>
                        </div>

                        <div
                            class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">Order summary</p>

                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <dl class="flex items-center justify-between gap-4">
                                        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Subtotal
                                        </dt>
                                        <dd class="text-base font-medium text-gray-900 dark:text-white">
                                            {{ format_currency($this->subtotal) }}</dd>
                                    </dl>

                                    <dl class="flex items-center justify-between gap-4">
                                        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Savings</dt>
                                        <dd class="text-base font-medium text-green-600">
                                            -{{ format_currency($this->savings) }}</dd>
                                    </dl>
                                </div>

                                <dl
                                    class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                    <dd class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ format_currency($this->total) }}</dd>
                                </dl>
                            </div>

                            <button wire:navigate href='/shopping-cart/checkout'
                                class="flex w-full items-center justify-center rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proceed
                                to Checkout</button>

                            <div class="flex items-center justify-center gap-2">
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400"> or </span>
                                <a wire:navigate href="/products"
                                    class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline dark:text-primary-500">
                                    Continue Shopping
                                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
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
