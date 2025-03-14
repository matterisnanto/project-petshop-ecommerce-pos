<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</head>

<body class="flex flex-col min-h-screen">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Konten Halaman -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
{{-- carousel brand --}}
<script>
    function addToCart(productId) {
        fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    quantity: 1
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal menambahkan produk ke keranjang');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Perbarui dropdown keranjang dengan data terbaru dari server
                    updateCartDropdown(data.cart, data.total);
                } else {
                    alert('Gagal menambahkan produk ke keranjang: ' + (data.message || ''));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan produk ke keranjang.');
            });
    }

    function updateCartDropdown(cart, total) {
        const cartDropdown = document.querySelector('#myCartDropdown1');
        const emptyCartMessage = document.querySelector('#empty-cart-message');
        const cartTotalContainer = document.querySelector('#cart-total-container');
        const viewCartButtonContainer = document.querySelector('#view-cart-button-container');

        // Kosongkan dropdown keranjang
        cartDropdown.innerHTML = '';

        // Jika keranjang kosong, tampilkan pesan
        if (Object.keys(cart).length === 0) {
            const emptyCartMessage = document.createElement('div');
            emptyCartMessage.id = 'empty-cart-message';
            emptyCartMessage.className = 'flex items-center justify-center h-20';
            emptyCartMessage.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Keranjang kosong</p>';
            cartDropdown.appendChild(emptyCartMessage);
        } else {
            // Tambahkan produk ke dropdown
            for (const [id, item] of Object.entries(cart)) {
                const productElement = document.createElement('div');
                productElement.id = `cart-item-${id}`;
                productElement.className = 'grid grid-cols-2 p-4 border-b border-gray-200 dark:border-gray-700';
                productElement.innerHTML = `
                <div class="max-w-[200px]">
                    <a href="#" class="block truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">
                        ${item.name}
                    </a>
                    <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">Rp. ${item.price.toLocaleString()}</p>
                </div>
                <div class="flex items-center justify-end gap-4">
                    <div class="flex items-center gap-2">
                        <button onclick="decreaseQuantity(${id})" class="p-1.5 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                            </svg>
                        </button>
                        <input id="quantity-${id}" name="quantity-${id}" min="1" value="${item.quantity}" onchange="updateQuantityManual(${id})" class="w-12 px-2 py-1.5 text-sm text-center text-gray-900 border border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        <button onclick="increaseQuantity(${id})" class="p-1.5 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg>
                        </button>
                    </div>
                    <button onclick="removeFromCart(${id})" data-tooltip-target="tooltipRemoveItem1a" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                        <span class="sr-only"> Remove </span>
                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            `;
                cartDropdown.appendChild(productElement);
            }

            // Tambahkan total keseluruhan
            const totalContainer = document.createElement('div');
            totalContainer.id = 'cart-total-container';
            totalContainer.className = 'p-2 border-t border-gray-200 dark:border-gray-700';
            totalContainer.innerHTML = `
            <p class="text-lg font-bold text-gray-900 dark:text-white text-right">
                Total: Rp. <span id="cart-total">${total.toLocaleString()}</span>
            </p>
        `;
            cartDropdown.appendChild(totalContainer);
        }

        // Tambahkan tombol "Lihat Keranjang"
        const viewCartButton = document.createElement('div');
        viewCartButton.className = 'p-4';
        viewCartButton.innerHTML = `
        <a href="{{ route('cart.view') }}" title="" class="w-full inline-flex items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800" role="button">Lihat Keranjang</a>
    `;
        cartDropdown.appendChild(viewCartButton);
    }

    function updateQuantity(productId, quantity) {
        // Jika quantity kurang dari 1, hapus produk dari keranjang
        if (quantity < 1) {
            removeFromCart(productId);
            return;
        }

        fetch(`/cart/update/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    quantity: quantity
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memperbarui kuantitas');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update quantity input
                    const quantityInput = document.querySelector(`input[name="quantity-${productId}"]`);
                    if (quantityInput) quantityInput.value = quantity;

                    // Update total keseluruhan
                    updateCartTotal(data.total);
                } else {
                    alert('Gagal memperbarui kuantitas: ' + (data.message || ''));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui kuantitas.');
            });
    }

    function updateQuantityManual(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value);

            // Validasi quantity
            if (isNaN(quantity) || quantity < 1) {
                alert('Kuantitas harus lebih dari 0');
                input.value = 1; // Reset ke nilai default
                return;
            }

            updateQuantity(productId, quantity);
        }
    }

    function increaseQuantity(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value) + 1;
            input.value = quantity; // Langsung update nilai input
            updateQuantity(productId, quantity); // Kirim permintaan ke server
        }
    }

    function decreaseQuantity(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value) - 1;

            // Jika quantity kurang dari 1, hapus produk dari keranjang
            if (quantity < 1) {
                removeFromCart(productId);
                return;
            }

            input.value = quantity; // Langsung update nilai input
            updateQuantity(productId, quantity); // Kirim permintaan ke server
        }
    }

    function removeFromCart(productId) {
        fetch(`/cart/remove/${productId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hapus elemen produk dari DOM
                    const itemElement = document.querySelector(`#cart-item-${productId}`);
                    if (itemElement) itemElement.remove();

                    // Update total keseluruhan
                    updateCartTotal(data.total);

                    // Jika keranjang kosong, tampilkan pesan dan sembunyikan total
                    const cartItems = document.querySelectorAll('[id^="cart-item-"]');
                    if (cartItems.length === 0) {
                        const emptyCartMessage = document.createElement('div');
                        emptyCartMessage.id = 'empty-cart-message';
                        emptyCartMessage.className = 'flex items-center justify-center h-20';
                        emptyCartMessage.innerHTML =
                            '<p class="text-gray-500 dark:text-gray-400">Keranjang kosong</p>';

                        const cartDropdown = document.querySelector('#myCartDropdown1');
                        cartDropdown.insertBefore(emptyCartMessage, cartDropdown.firstChild);

                        // Hapus total container
                        const totalContainer = document.querySelector('#cart-total-container');
                        if (totalContainer) {
                            totalContainer.remove();
                        }
                    }
                } else {
                    alert('Gagal menghapus produk!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus produk.');
            });
    }

    function updateCartTotal(total) {
        const totalElement = document.querySelector('#cart-total');
        if (totalElement) {
            totalElement.textContent = total.toLocaleString();
        }
    }
</script>

</html>
