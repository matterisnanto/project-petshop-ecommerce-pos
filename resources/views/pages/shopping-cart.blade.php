<div>
    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">Keranjang Belanja</h2>
            <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full">
                    <div id="shoppingCart" class="space-y-6">
                        <!-- Example of a single cart item (static version) -->
                        <div
                            class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6 relative">
                            <!-- Remove Button -->
                            <button
                                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 dark:hover:text-red-500">
                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                </svg>
                            </button>

                            <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                <!-- Product Image -->
                                <a href="#" class="shrink-0 md:order-1">
                                    <img class="h-20 w-20 dark:hidden" src="img/product-thumbnail.jpg"
                                        alt="product image" />
                                    <img class="hidden h-20 w-20 dark:block" src="img/product-thumbnail.jpg"
                                        alt="product image" />
                                </a>

                                <!-- Quantity Controller -->
                                <div class="flex items-center justify-between md:order-3 md:justify-end">
                                    <div class="flex items-center">
                                        <!-- Decrement Button -->
                                        <button
                                            class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                            <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>

                                        <!-- Quantity Input -->
                                        <input id="quantity-1" name="quantity-1" min="1" max="10"
                                            value="1"
                                            class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white" />

                                        <!-- Increment Button -->
                                        <button
                                            class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                            <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Product Name and Barcode -->
                                <div class="w-full min-w-0 flex-1 md:order-2 md:max-w-md md:ml-4">
                                    <a href="#"
                                        class="text-base font-semibold text-gray-900 hover:underline dark:text-white">
                                        Nama Produk
                                    </a>
                                    <p class="text-gray-900 dark:text-white">Barcode: 123456789</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">Rp. 100.000</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Total -->
                        <div id="shopping-cart-total-container" class="space-y-6">
                            <dl
                                class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-base font-bold text-gray-900 dark:text-white">
                                    Rp. 100.000
                                </dd>
                            </dl>

                            <!-- Checkout Button -->
                            <div id="checkout-button" class="space-y-6">
                                <a href="#"
                                    class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-600 dark:focus:ring-primary-800">
                                    Proses ke Checkout
                                </a>
                            </div>
                        </div>

                        <!-- Empty Cart State (hidden by default) -->
                        <div id="empty-shopping-cart-message" class="flex flex-col items-center justify-center hidden">
                            <img src="img/kucing-cape.png" alt="Kucing Cape" class="w-100 mb-4">
                            <div class="flex items-center justify-center h-10">
                                <p class="text-2xl text-gray-500 dark:text-gray-400">Keranjang kosong</p>
                            </div>
                        </div>

                        <!-- Back to Shopping Button (shown when cart is empty) -->
                        <div id="back-shopping" class="space-y-6 hidden">
                            <a href="#"
                                class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-600 dark:focus:ring-primary-800">
                                Kembali Belanja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
