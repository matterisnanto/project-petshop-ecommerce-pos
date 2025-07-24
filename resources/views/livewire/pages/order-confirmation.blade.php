<div>
    <section class="bg-white py-2 antialiased dark:bg-gray-900 md:py-4">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <!-- Order Progress Steps -->
            <ol
                class="items-center flex w-full max-w-2xl text-center text-sm font-medium text-gray-500 dark:text-gray-400 sm:text-base">
                <li
                    class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-400 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center after:mx-2 after:text-gray-400 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Cart
                    </span>
                </li>
                <li
                    class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-400 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center after:mx-2 after:text-gray-400 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Checkout
                    </span>
                </li>
                <li class="flex shrink-0 items-center">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Order Confirmation
                </li>
            </ol>

            <div class="mt-8">
                <!-- Order Confirmation Card -->
                <div
                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-6 flex items-center gap-4">
                        <div
                            class="flex h-7 sm:h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Order
                                Confirmed!
                            </h2>
                            <p class="text-sm sm:text-lg text-gray-500 dark:text-gray-400">Your detail_order
                                #{{ $transaction->trx_id }} has been
                                placed successfully.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                                <h3 class="mb-2 text-base sm:text-lg font-medium text-gray-900 dark:text-white">Order
                                    Details</h3>
                                <dl class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Order ID</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            #{{ $transaction->trx_id }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Resi Number
                                        </dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->package_resi_number }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Date</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->created_at->format('d M Y') }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Payment Method
                                        </dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->paymentMethod->name }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Total Amount
                                        </dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ format_currency($transaction->grand_total_amount) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div
                                class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                                <h3 class="mb-2 text-base sm:text-lg font-medium text-gray-900 dark:text-white">Shipping
                                    Details</h3>
                                <dl class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Name</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->name }}
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Phone</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->phone }}
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Address</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->complete_address }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Courier</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            @php
                                                $shippingService = is_array($transaction->shipping_service)
                                                    ? $transaction->shipping_service
                                                    : json_decode($transaction->shipping_service, true);
                                            @endphp
                                            {{ Str::upper($shippingService['courier'] ?? $transaction->courier) }}
                                            ({{ $shippingService['description'] ?? 'N/A' }})
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Estimated
                                            Delivery</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->estimated_delivery }} day</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                            <h3 class="mb-4 text-base sm:text-lg font-medium text-gray-900 dark:text-white">Order Items
                            </h3>
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-left text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="bg-gray-100 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Product</th>
                                            <th scope="col" class="sm:px-2 py-3">Qty</th>
                                            <th scope="col" class="px-6 py-3 text-right">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transaction->orders as $item)
                                            <tr class="border-b bg-white dark:border-gray-700 dark:bg-gray-800">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-4">
                                                        <div class="aspect-square h-10 w-10 shrink-0">
                                                            <img class="h-full w-full object-cover"
                                                                src="{{ $item->product->image_url }}"
                                                                alt="{{ $item->product->name }}">
                                                        </div>
                                                        <a href="#"
                                                            class="font-medium text-gray-900 hover:underline dark:text-white">{{ $item->product->name }}</a>
                                                    </div>
                                                </td>
                                                <td class="px-2 py-4">x{{ $item->quantity }}</td>
                                                <td
                                                    class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                                                    {{ format_currency($item->unit_price * $item->quantity) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                            <h3 class="mb-4 text-base sm:text-lg font-medium text-gray-900 dark:text-white">Order
                                Summary</h3>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Subtotal</dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        {{ format_currency($transaction->sub_total_amount) }}</dd>
                                </div>
                                @if ($transaction->discount_amount > 1)
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Savings</dt>
                                        <dd
                                            class="text-sm sm:text-base font-medium text-gray-900 dark:text-white text-green-600">
                                            -{{ format_currency($transaction->discount_amount) }}</dd>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Shipping</dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        {{ format_currency($transaction->shipping_cost) }}</dd>
                                </div>
                                <div
                                    class="flex items-center justify-between border-t border-gray-200 pt-2 dark:border-gray-600">
                                    <dt class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Total</dt>
                                    <dd class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">
                                        {{ format_currency($transaction->grand_total_amount) }}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Order Status -->
                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                            <h3 class="mb-2 text-base sm:text-lg font-medium text-gray-900 dark:text-white">Order
                                Status</h3>
                            <div class="flex items-center gap-4">
                                <div class="h-1.5 sm:h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-600">
                                    <div class="h-1.5 sm:h-2.5 rounded-full bg-primary-600" style="width: 25%"></div>
                                </div>
                                <span
                                    class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Processing</span>
                            </div>
                            <p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                Your detail_order will be processed within 24 hours during working days.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex flex-col gap-4 sm:flex-row">
                    <a wire:navigate href="/trx-check"
                        class="inline-flex items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        Track Your Order
                    </a>
                    <a wire:navigate href="/product"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-center text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                        Continue Shopping
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>
