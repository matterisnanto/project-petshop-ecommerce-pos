<div>
    <!-- Search Section -->
    <section class="bg-white mt-20 py-2 antialiased dark:bg-gray-900 md:py-4">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mx-auto max-w-screen-md text-center">
                <h2 class="mb-4 text-3xl sm:text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                    Check Your Transaction
                </h2>
                <p class="mb-6 text-sm sm:text-lg font-light text-gray-500 dark:text-gray-400">
                    Trx ID (Transaction ID) is a unique code provided after a successful transaction.
                    You can use this code to track, verify, or check the status of your transaction.
                </p>
                <form wire:submit.prevent="search" class="mx-auto max-w-screen-sm">
                    <div class="flex items-center gap-3">
                        <div class="relative w-full">
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path id="primary" d="M5,9H20M4,15H19M17.52,3,12.69,21M6.48,21,11.31,3"
                                        style="fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;">
                                    </path>
                                </svg>
                            </div>
                            <input
                                class="block p-3 pl-10 w-full text-sm sm:text-base text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Enter your transaction code" wire:model="transactionId" id="transaction_id"
                                required>
                        </div>
                        <button type="submit"
                            class="py-3 px-5 text-sm sm:text-base font-medium text-center text-white rounded-lg bg-primary-500 hover:bg-primary-600 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            Search
                        </button>
                    </div>
                    @error('transactionId')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>
    </section>

    <!-- Error Modal -->
    <div id="ErrorModal" tabindex="-1" aria-hidden="true"
        class="{{ $showErrorModal ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-center justify-center bg-black/50">
        <div class="relative p-4 w-full max-w-md">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800 p-6">
                <div class="flex justify-end mb-4">
                    <button type="button" wire:click="closeErrorModal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-col items-center text-center">
                    <img src="/img/kucing-cape.png" alt="Transaction Not Found" class="w-32 mb-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $error ?? 'Transaction Error' }}
                    </h2>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                        Please check your transaction ID and try again.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($transaction)
        <!-- Transaction Details Section -->
        <section class="bg-white py-4 antialiased dark:bg-gray-900 md:py-8">
            <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                <!-- Transaction Header -->
                <div class="bg-primary-100 dark:bg-gray-800 w-full py-6 rounded-lg mb-8">
                    <div class="px-4 mx-auto max-w-screen-sm text-center">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                            Tracking Order #{{ $transaction->trx_id }}
                        </h2>
                    </div>
                </div>

                <div
                    class="sm:rounded-lg sm:border sm:border-gray-200 sm:bg-gray-50 sm:shadow-sm dark:bg-gray-800 w-full sm:p-6 rounded-lg mb-2">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Order Details -->
                        <div
                            class="rounded-lg shadow-sm sm:shadow-none border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                            <h3 class="mb-2 text-base sm:text-lg font-medium text-gray-900 dark:text-white">Order
                                Details</h3>
                            <dl class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Order ID</dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        {{ $transaction->trx_id }}
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Date</dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        {{ $transaction->created_at->format('d M Y, H:i') }}
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Payment Method
                                    </dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        {{ $transaction->paymentmethod->name ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Total Amount</dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($transaction->grand_total_amount, 0, ',', '.') }}
                                    </dd>
                                </div>
                                @if ($transaction->promocode)
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Promo Code
                                        </dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->promocode->code }} (-Rp
                                            {{ number_format($transaction->discount_amount, 0, ',', '.') }})
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Shipping Details -->
                        <div
                            class="rounded-lg shadow-sm sm:shadow-none border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
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
                                        {{ $transaction->complete_address }}, {{ $transaction->city_regency }},
                                        {{ $transaction->province }} {{ $transaction->post_code }}
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Courier</dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        @php
                                            $shippingService = is_array($transaction->shipping_service)
                                                ? $transaction->shipping_service
                                                : json_decode($transaction->shipping_service, true);
                                        @endphp

                                        {{ $shippingService['courier'] ?? $transaction->courier }}
                                        ({{ $shippingService['description'] ?? 'N/A' }})
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Shipping Cost</dt>
                                    <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}
                                    </dd>
                                </div>
                                @if ($transaction->estimated_delivery)
                                    <div class="flex items-center justify-between">
                                        <dt class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Estimated
                                            Delivery</dt>
                                        <dd class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->estimated_delivery }} day(s)
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="mt-4 sm:mt-8 lg:flex lg:gap-8">
                    <!-- Order Items -->
                    <div
                        class="w-full rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:max-w-xl xl:max-w-2xl">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items
                                ({{ count($transaction->orders) }})</h3>
                        </div>

                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($transaction->orders as $order)
                                <div class="p-4">
                                    <div class="flex gap-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <img class="h-20 w-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                                                src="{{ $order->product->image_url ?? '/img/default-product.png' }}"
                                                alt="{{ $order->product->name ?? 'Product image' }}"
                                                onerror="this.src='/img/default-product.png'">
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                                            <div>
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $order->product->name ?? 'Product' }}
                                                </h4>
                                                @if ($order->product->weight)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ number_format($order->product->weight / 1000, 2) }} kg
                                                    </p>
                                                @endif
                                            </div>

                                            <!-- Quantity -->
                                            <div class="mt-2">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Quantity:
                                                </span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $order->quantity }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-base font-semibold text-gray-900 dark:text-white">
                                                Rp
                                                {{ number_format($order->unit_price * $order->quantity, 0, ',', '.') }}
                                            </div>
                                            @if ($order->quantity > 1)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Rp {{ number_format($order->unit_price, 0, ',', '.') }} each
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Additional Actions -->
                                    <div
                                        class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                        <button type="button" wire:navigate
                                            href="/products/{{ $order->product->slug }}"
                                            class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300">
                                            Buy Again
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-gray-50 p-6 dark:bg-gray-700/50 rounded-b-lg">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Subtotal</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($transaction->sub_total_amount, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if ($transaction->discount_amount > 0)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Discount</span>
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                            -Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Shipping</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div
                                    class="pt-3 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center">
                                    <span class="text-base font-semibold text-gray-900 dark:text-white">Total</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($transaction->grand_total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="mt-4 grow sm:mt-8 lg:mt-0">
                        <div
                            class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">Order History
                            </h3>
                            <ol class="relative ms-3 border-s border-gray-200 dark:border-gray-700">
                                <!-- Current Status - Payment Verification -->
                                <li class="mb-8 ms-6">
                                    <span
                                        class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full {{ $transaction->is_paid ? 'bg-green-100 dark:bg-green-900' : 'bg-primary-100 dark:bg-primary-900' }} ring-8 ring-white dark:ring-gray-800">
                                        <svg class="h-4 w-4 {{ $transaction->is_paid ? 'text-green-600 dark:text-green-300' : 'text-primary-600 dark:text-primary-300' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M5 11.917 9.724 16.5 19 7.5" />
                                        </svg>
                                    </span>
                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                                        @if ($transaction->is_paid)
                                            Payment Verified
                                        @else
                                            Payment Verification Process
                                        @endif
                                    </h4>
                                    <p class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400">
                                        {{ $transaction->updated_at->format('d M Y, H:i') }}
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                        @if ($transaction->is_paid)
                                            Your payment has been verified
                                        @else
                                            Your order is in the payment verification process
                                        @endif
                                    </p>
                                </li>

                                <!-- Ready to Ship Status -->
                                <li class="mb-8 ms-6">
                                    <span
                                        class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full {{ $transaction->is_paid ? ($transaction->package_resi_number && $transaction->package_resi_number !== 'Being Processed' ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-primary-900') : 'bg-gray-100 dark:bg-gray-700' }} ring-8 ring-white dark:ring-gray-800">
                                        @if (
                                            $transaction->is_paid &&
                                                $transaction->package_resi_number &&
                                                $transaction->package_resi_number !== 'Being Processed')
                                            <svg class="h-4 w-4 text-green-600 dark:text-green-300" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m5 12 4.7 4.5 9.3-9" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 {{ $transaction->is_paid ? 'text-primary-600 dark:text-primary-300' : 'text-gray-500 dark:text-gray-400' }}"
                                                aria-hidden="true" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path
                                                        d="M7.50626 15.2647C7.61657 15.6639 8.02965 15.8982 8.4289 15.7879C8.82816 15.6776 9.06241 15.2645 8.9521 14.8652L7.50626 15.2647ZM6.07692 7.27442L6.79984 7.0747V7.0747L6.07692 7.27442ZM4.7037 5.91995L4.50319 6.64265L4.7037 5.91995ZM3.20051 4.72457C2.80138 4.61383 2.38804 4.84762 2.2773 5.24675C2.16656 5.64589 2.40035 6.05923 2.79949 6.16997L3.20051 4.72457ZM20.1886 15.7254C20.5895 15.6213 20.8301 15.2118 20.7259 14.8109C20.6217 14.41 20.2123 14.1695 19.8114 14.2737L20.1886 15.7254ZM10.1978 17.5588C10.5074 18.6795 9.82778 19.8618 8.62389 20.1747L9.00118 21.6265C10.9782 21.1127 12.1863 19.1239 11.6436 17.1594L10.1978 17.5588ZM8.62389 20.1747C7.41216 20.4896 6.19622 19.7863 5.88401 18.6562L4.43817 19.0556C4.97829 21.0107 7.03196 22.1383 9.00118 21.6265L8.62389 20.1747ZM5.88401 18.6562C5.57441 17.5355 6.254 16.3532 7.4579 16.0403L7.08061 14.5885C5.10356 15.1023 3.89544 17.0911 4.43817 19.0556L5.88401 18.6562ZM7.4579 16.0403C8.66962 15.7254 9.88556 16.4287 10.1978 17.5588L11.6436 17.1594C11.1035 15.2043 9.04982 14.0768 7.08061 14.5885L7.4579 16.0403ZM8.9521 14.8652L6.79984 7.0747L5.354 7.47414L7.50626 15.2647L8.9521 14.8652ZM4.90421 5.19725L3.20051 4.72457L2.79949 6.16997L4.50319 6.64265L4.90421 5.19725ZM6.79984 7.0747C6.54671 6.15847 5.8211 5.45164 4.90421 5.19725L4.50319 6.64265C4.92878 6.76073 5.24573 7.08223 5.354 7.47414L6.79984 7.0747ZM11.1093 18.085L20.1886 15.7254L19.8114 14.2737L10.732 16.6332L11.1093 18.085Z"
                                                        fill="#1C274C"></path>
                                                    <path
                                                        d="M9.56541 8.73049C9.0804 6.97492 8.8379 6.09714 9.24954 5.40562C9.66119 4.71409 10.5662 4.47889 12.3763 4.00849L14.2962 3.50955C16.1062 3.03915 17.0113 2.80394 17.7242 3.20319C18.4372 3.60244 18.6797 4.48023 19.1647 6.2358L19.6792 8.09786C20.1642 9.85343 20.4067 10.7312 19.995 11.4227C19.5834 12.1143 18.6784 12.3495 16.8683 12.8199L14.9484 13.3188C13.1384 13.7892 12.2333 14.0244 11.5203 13.6252C10.8073 13.2259 10.5648 12.3481 10.0798 10.5926L9.56541 8.73049Z"
                                                        stroke="#1C274C" stroke-width="1.5"></path>
                                                </g>
                                            </svg>
                                        @endif
                                    </span>
                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                                        @if (
                                            $transaction->package_resi_number &&
                                                $transaction->package_resi_number !== 'Being Processed' &&
                                                $transaction->package_resi_number !== 'Accepted')
                                            Shipped with Resi: {{ $transaction->package_resi_number }}
                                        @elseif ($transaction->package_resi_number && $transaction->package_resi_number == 'Accepted')
                                            Your order has been received
                                        @elseif ($transaction->package_resi_number && $transaction->package_resi_number == 'Being Processed')
                                            Processing Order
                                        @else
                                            Ready To Ship
                                        @endif
                                    </h4>
                                    {{-- <p class="text-xs sm:text-sm font-normal text-gray-500 dark:text-gray-400">
                                        @if ($transaction->package_resi_number && $transaction->package_resi_number !== 'Being Processed')
                                            {{ $transaction->shipped_at ? $transaction->shipped_at->format('d M Y, H:i') : 'Today' }}
                                        @else
                                            Today
                                        @endif
                                    </p> --}}
                                    <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                        @if (
                                            $transaction->package_resi_number &&
                                                $transaction->package_resi_number !== 'Being Processed' &&
                                                $transaction->package_resi_number !== 'Accepted')
                                            Your package has been shipped
                                            @if ($transaction->shipping_service && is_array($transaction->shipping_service))
                                                via {{ $transaction->shipping_service['courier'] ?? 'courier' }}
                                            @endif
                                        @elseif ($transaction->package_resi_number && $transaction->package_resi_number == 'Accepted')
                                            Your order has been received
                                        @elseif ($transaction->package_resi_number && $transaction->package_resi_number == 'Being Processed')
                                            waiting for payment verification
                                        @else
                                            Your order is being prepared for shipment
                                        @endif
                                    </p>
                                </li>

                                <!-- Shipped Status (jika sudah ada resi) -->
                                @if (
                                    $transaction->package_resi_number &&
                                        $transaction->package_resi_number !== 'Being Processed' &&
                                        $transaction->package_resi_number !== 'Accepted')
                                    <li class="mb-8 ms-6">
                                        <span
                                            class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full {{ $transaction->is_paid ? ($transaction->package_resi_number && $transaction->package_resi_number == 'Accepted' ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-primary-900') : 'bg-gray-100 dark:bg-gray-700' }} ring-8 ring-white dark:ring-gray-800">
                                            @if ($transaction->is_paid && $transaction->package_resi_number && $transaction->package_resi_number == 'Accepted')
                                                <svg class="h-4 w-4 text-green-600 dark:text-green-300"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m5 12 4.7 4.5 9.3-9" />
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
                                                </svg>
                                            @endif
                                        </span>
                                        <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                                            Shipped
                                        </h4>
                                        @if ($transaction->shipping_service && is_array($transaction->shipping_service))
                                            <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                                @if (
                                                    $transaction->package_resi_number &&
                                                        $transaction->package_resi_number !== 'Being Processed' &&
                                                        $transaction->package_resi_number !== 'Accepted')
                                                    Via {{ $transaction->shipping_service['courier'] }}
                                                    ({{ $transaction->shipping_service['service'] }})
                                                    @if (isset($transaction->shipping_service['etd']))
                                                        - Estimated {{ $transaction->shipping_service['etd'] }} day(s)
                                                        delivery
                                                    @endif
                                                @else
                                                    your order has been received
                                                @endif
                                            </p>
                                        @endif
                                    </li>
                                @endif

                                <!-- Order Placed -->
                                <li class="ms-6">
                                    <span
                                        class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 ring-8 ring-white dark:bg-gray-700 dark:ring-gray-800">
                                        <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </span>
                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">Order
                                        Placed</h4>
                                </li>
                            </ol>

                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                                <button type="button"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    Contact Support
                                </button>
                                <a href="/"
                                    class="w-full rounded-lg bg-primary-500 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                    Back to shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
