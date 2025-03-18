@extends('layouts.store')

@section('title', 'Shopping Cart')

@section('content')

    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Keranjang Belanja</h2>

            <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full">
                    <div id="shoppingcart" class="space-y-6">
                        @if (count(session('cart', [])) > 0)
                            @foreach (session('cart') as $id => $item)
                                <div id="cart-item-{{ $id }}"
                                    class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6 relative">
                                    <!-- Tombol Remove -->
                                    <button onclick="removeFromShoppingCart({{ $id }})"
                                        class="absolute top-2 right-2 text-gray-500 hover:text-red-600 dark:hover:text-red-500">
                                        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                        </svg>
                                    </button>

                                    <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                        <!-- Gambar Produk -->
                                        <a href="#" class="shrink-0 md:order-1">
                                            <img class="h-20 w-20 dark:hidden" src="{{ asset($item['thumbnail']) }}"
                                                alt="imac image" />
                                            <img class="hidden h-20 w-20 dark:block"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg"
                                                alt="imac image" />
                                        </a>

                                        <!-- Quantity Controller -->
                                        <div class="flex items-center justify-between md:order-3 md:justify-end">
                                            <div class="flex items-center">
                                                <!-- Tombol Decrement -->
                                                <button onclick="decreaseQuantity({{ $id }})"
                                                    class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 18 2">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                    </svg>
                                                </button>

                                                <!-- Input Quantity -->
                                                <input id="quantity-{{ $id }}" name="quantity-{{ $id }}"
                                                    min="1" value="{{ $item['quantity'] }}"
                                                    onchange="updateQuantityManual({{ $id }})"
                                                    class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white" />

                                                <!-- Tombol Increment -->
                                                <button onclick="increaseQuantity({{ $id }})"
                                                    class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 18 18">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Sub Total -->
                                            <div class="text-end md:order-4 md:w-32">
                                                <p class="text-base font-bold text-gray-900 dark:text-white">
                                                    Rp. <span
                                                        id="subtotal-{{ $id }}">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Nama Produk dan Barcode -->
                                        <div class="w-full min-w-0 flex-1 space-y-2 md:order-2 md:max-w-md">
                                            <a href="#"
                                                class="text-base font-semibold text-gray-900 hover:underline dark:text-white">
                                                {{ $item['name'] }}
                                            </a>
                                            <p class="text-gray-900 dark:text-white">{{ $item['barcode'] }}</p>
                                            <p>Rp. {{ number_format($item['price'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Jika Keranjang Kosong -->
                            <div id="empty-cart-message" class="flex items-center justify-center h-10">
                                <p class="text-gray-500 dark:text-gray-400">Keranjang kosong</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <dl
                            class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                            <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                            <dd id="cart-total" class="text-base font-bold text-gray-900 dark:text-white">
                                Rp.
                                {{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], session('cart'))), 0, ',', '.') }}
                            </dd>
                        </dl>

                        <a href="#"
                            class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proses
                            ke Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function updateQuantity(productId, quantity) {
            $.ajax({
                url: '/update-cart/' + productId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        // Update subtotal
                        $('#subtotal-' + productId).text(formatRupiah(response.subtotal));
                        // Update total
                        // $('#total').text(formatRupiah(response.total));
                    }
                }
            });
        }

        function increaseQuantity(productId) {
            var input = $('#quantity-' + productId);
            var newQuantity = parseInt(input.val()) + 1;
            input.val(newQuantity);
            updateQuantity(productId, newQuantity);
        }

        function decreaseQuantity(productId) {
            var input = $('#quantity-' + productId);
            var newQuantity = parseInt(input.val()) - 1;
            if (newQuantity < 1) newQuantity = 1;
            input.val(newQuantity);
            updateQuantity(productId, newQuantity);
        }

        function updateQuantityManual(productId) {
            var input = $('#quantity-' + productId);
            var newQuantity = parseInt(input.val());
            if (newQuantity < 1) newQuantity = 1;
            input.val(newQuantity);
            updateQuantity(productId, newQuantity);
        }

        function removeFromShoppingCart(productId) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
                fetch(`/cart/remove/${productId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hapus elemen produk dari DOM
                            document.getElementById(`cart-item-${productId}`).remove();

                            // Perbarui total harga
                            document.getElementById('cart-total').innerText = `Rp. ${data.total.toLocaleString()}`;

                            // Jika keranjang kosong, tampilkan pesan
                            if (data.cart_count === 0) {
                                document.getElementById('shoppingcart').innerHTML = `
                                <div id="empty-cart-message" class="flex items-center justify-center h-10">
                                    <p class="text-gray-500 dark:text-gray-400">Keranjang kosong</p>
                                </div>
                            `;
                            }
                        }
                    });
            }
        }

        function formatRupiah(angka) {
            var reverse = angka.toString().split('').reverse().join(''),
                ribuan = reverse.match(/\d{1,3}/g);
            ribuan = ribuan.join('.').split('').reverse().join('');
            return 'Rp. ' + ribuan;
        }
    </script>
@endsection
