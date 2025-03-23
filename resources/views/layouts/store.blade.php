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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</head>

<body class="flex flex-col min-h-screen dark:dark:bg-gray-900">
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
    window.imageUrl = "{{ asset('img/kucing-cape.png') }}";

    function removeProduct(productId) {
        // Panggil fungsi removeFromCart
        removeFromCart(productId);

        // Cek apakah pengguna berada di halaman shopping-cart
        if (window.location.pathname.includes('shopping-cart')) {
            // Panggil fungsi removeFromShoppingCart hanya jika berada di halaman shopping-cart
            removeFromShoppingCart(productId);
        }
    }

    //cart dropdown
    function addToCart(productId) {
        // Ambil nilai quantity dari input
        const quantityInput = document.getElementById('product-detail-quantity');
        const quantity = quantityInput ? quantityInput.value : 1;

        // Kirim permintaan AJAX ke server
        fetch(`/cart/add/${productId}`, {
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
                    // Jika respons tidak OK (misalnya, status 400), lempar error
                    return response.json().then(err => {
                        throw new Error(err.message || 'Gagal menambahkan produk ke keranjang');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Produk berhasil ditambahkan ke keranjang');
                    // Perbarui tampilan keranjang jika diperlukan
                    updateCartDropdown(data.cart, data.total);
                } else {
                    alert('Gagal menambahkan produk ke keranjang: ' + (data.message || ''));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Terjadi kesalahan saat menambahkan produk ke keranjang.');
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
            emptyCartMessage.className = 'flex items-center justify-center h-60';
            emptyCartMessage.innerHTML = `<img src="${window.imageUrl}" alt="Kucing Cape" class="w-50 mb-2">
                            <p class="sm:text-xl text-gray-500 dark:text-gray-400">Keranjang kosong</p>`;
            cartDropdown.appendChild(emptyCartMessage);
        } else {
            // Tambahkan produk ke dropdown
            for (const [id, item] of Object.entries(cart)) {
                const productElement = document.createElement('div');
                productElement.id = `cart-item-${id}`;
                productElement.className = 'grid grid-cols-2 p-4 border-b border-gray-200 dark:border-gray-700';
                productElement.innerHTML = `
                <div class="max-w-[200px]">
                    <a href="/product/${item.slug}" class="block truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">
                        ${item.name}
                    </a>
                    <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">Rp. ${item.price.toLocaleString()}</p>
                </div>
                <div class="flex items-center justify-end gap-4">
                    <div class="flex items-center gap-2">
                        <button onclick="decreaseQuantityCart(${id})" class="p-1.5 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                            </svg>
                        </button>
                        <input id="cart-quantity-${id}" name="cart-quantity-${id}" min="1" max="${item.stock}" value="${item.quantity}" onchange="updateQuantityCartManual(${id})" class="w-12 px-2 py-1.5 text-sm text-center text-gray-900 border border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        <button onclick="increaseQuantityCart(${id})" class="p-1.5 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg>
                        </button>
                    </div>
                    <button onclick="removeProduct(${id})" data-tooltip-target="tooltipRemoveItem1a" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
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

            // Tambahkan tombol "Lihat Keranjang" hanya jika keranjang tidak kosong
            const viewCartButton = document.createElement('div');
            viewCartButton.id = 'view-cart-button-container'; // Tambahkan ID untuk memudahkan penghapusan
            viewCartButton.className = 'p-4';
            viewCartButton.innerHTML = `
            <a href="{{ route('cart.view') }}" title="" class="w-full inline-flex items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800" role="button">Lihat Keranjang</a>
        `;
            cartDropdown.appendChild(viewCartButton);
        }
    }

    function updateCartQuantity(productId, quantity) {
        if (quantity < 1) {
            removeProduct(productId);
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
                    // Perbarui quantity input
                    const quantityInput = document.querySelector(`input[name="cart-quantity-${productId}"]`);
                    if (quantityInput) quantityInput.value = quantity;

                    // Perbarui total keseluruhan
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

    function updateQuantityCartManual(productId) {
        const input = document.querySelector(`input[name="cart-quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value);
            const maxStock = parseInt(input.getAttribute('max'));

            // Validasi quantity
            if (isNaN(quantity) || quantity < 1) {
                alert('Kuantitas harus lebih dari 0');
                input.value = 1; // Reset ke nilai default
                return;
            }

            if (quantity > maxStock) {
                alert('Kuantitas tidak boleh melebihi stok yang tersedia.');
                input.value = maxStock; // Reset ke nilai maksimum
                return;
            }

            updateCartQuantity(productId, quantity);
            updateShoppingCartQuantity(productId, quantity);
        }
    }

    function increaseQuantityCart(productId) {
        const input = document.querySelector(`input[name="cart-quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value) + 1;
            const maxStock = parseInt(input.getAttribute('max'));

            if (quantity > maxStock) {
                alert('Kuantitas tidak boleh melebihi stok yang tersedia.');
                return;
            }

            input.value = quantity; // Langsung update nilai input
            updateCartQuantity(productId, quantity);
            updateShoppingCartQuantity(productId, quantity); // Kirim permintaan ke server
        }
    }

    function decreaseQuantityCart(productId) {
        const input = document.querySelector(`input[name="cart-quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value) - 1;

            // Jika quantity kurang dari 1, hapus produk dari keranjang
            if (quantity < 1) {
                removeProduct(productId);
                return;
            }

            input.value = quantity; // Langsung update nilai input
            updateCartQuantity(productId, quantity);
            updateShoppingCartQuantity(productId, quantity); // Kirim permintaan ke server
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


                    // Periksa apakah keranjang kosong
                    const cartItems = document.querySelectorAll('[id^="cart-item-"]');
                    if (cartItems.length === 0) {
                        // Tampilkan pesan keranjang kosong
                        const emptyCartMessage = document.createElement('div');
                        emptyCartMessage.id = 'empty-cart-message';
                        emptyCartMessage.className = 'flex flex-col items-center justify-center h-50';
                        emptyCartMessage.innerHTML =
                            `<img src="${window.imageUrl}" alt="Kucing Cape" class="w-50 mb-2">
                            <p class="sm:text-xl text-gray-500 dark:text-gray-400">Keranjang kosong</p>`;

                        const cartDropdown = document.querySelector('#myCartDropdown1');
                        cartDropdown.insertBefore(emptyCartMessage, cartDropdown.firstChild);

                        // Hapus total container
                        const totalContainer = document.querySelector('#cart-total-container');
                        if (totalContainer) {
                            totalContainer.remove();
                        }

                        // Hapus tombol "Lihat Keranjang"
                        const viewCartButton = document.querySelector('#view-cart-button-container');
                        if (viewCartButton) {
                            viewCartButton.remove();
                        }
                    }
                    // window.location.reload();
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



    //shoppingcart
    function updateShoppingCart(cart, total) {
        const shoppingCart = document.querySelector('#shoppingCart');
        const emptyShoppingCartMessage = document.querySelector('#empty-shopping-cart-message');
        const backShoppingButton = document.querySelector('#back-shopping');
        const totalShoppingCartContainer = document.querySelector('#shopping-cart-total-container');



        shoppingCart.innerHTML = '';

        if (Object.keys(cart).length === 0) {
            const emptyShoppingCartMessage = document.createElement('div');
            emptyShoppingCartMessage.id = 'empty-shopping-cart-message';
            emptyShoppingCartMessage.className = 'flex flex-col items-center justify-center h-50';
            emptyShoppingCartMessage.innerHTML =
                '<img src="${window.imageUrl}" alt="Kucing Cape" class="w-100 mb-4"> <div class = "flex items-center justify-center h-10"><p class = "text-4xl text-gray-500 dark:text-gray-400">Keranjang kosong</p></div >';
            shoppingCart.appendChild(emptyShoppingCartMessage);

            const backShoppingButton = document.createElement('div');
            backShoppingButton.id = 'back-shopping';
            backShoppingButton.className = 'space-y-6';
            backShoppingButton.innerHTML = `
            <a href="{{ route('product') }}" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Kembali Belanja</a>
        `;
            shoppingCart.appendChild(backShoppingButton);
        } else {
            for (const [id, item] of Object.entries(cart)) {
                const productShoppingCartElement = document.createElement('div');
                productElement.id = `shoppingcart-item-${id}`;
                productElement.className =
                    'rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6 relative';
                productElement.innerHTML = `
                <button onclick="removeFromCart(${id})" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 dark:hover:text-red-500">
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </button>
                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                    <a href="#" class="shrink-0 md:order-1">
                        <img class="h-20 w-20 dark:hidden" src="${item.thumbnail}" alt="imac image" />
                        <img class="hidden h-20 w-20 dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg" alt="imac image" />
                    </a>
                    <div class="flex items-center justify-between md:order-3 md:justify-end">
                        <div class="flex items-center">
                            <button onclick="decreaseShoppingCartQuantity(${id})"
                                class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 18 2">
                                    <path stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                </svg>
                            </button>
                            <input id="quantity-${id}" name="quantity-${id}"
                                min="1" value="${item.quantity}"
                                onchange="updateShoppingCartQuantityManual(${id})"
                                class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white" />
                            <button onclick="increaseShoppingCartQuantity(${id})"
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
                            <p class="text-base font-bold text-gray-900 dark:text-white">Rp. <span id="subtotal-${id}">${(item.price * item.quantity).toLocaleString()}</span></p>
                        </div>
                    </div>
                    <div class="w-full min-w-0 flex-1 space-y-2 md:order-2 md:max-w-md md:ml-4">
                        <a href="#" class="text-base font-semibold text-gray-900 hover:underline dark:text-white">${item.name}</a>
                        <p class="text-gray-900 dark:text-white">${item.barcode}</p>
                    </div>
                </div>
            `;
                shoppingCart.appendChild(productShoppingCartElement);
            }

            const totalShoppingCartContainer = document.createElement('div');
            totalContainer.id = 'shopping-cart-total-container';
            totalContainer.className = 'space-y-6';
            totalContainer.innerHTML = `
            <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                <dd class="text-base font-bold text-gray-900 dark:text-white" id="shopping-cart-total">Rp. <span>${total.toLocaleString()}</span></dd>
            </dl>
            <a href="#" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proses ke Checkout</a>
        `;
            shoppingCart.appendChild(totalShoppingCartContainer);

            const checkoutButton = document.createElement('div');
            checkoutButton.id = 'checkout-button';
            checkoutButton.className = 'space-y-6';
            checkoutButton.innerHTML = ` <a href="#"
                                        class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proses
                                        ke Checkout</a>`;
            shoppingCart.appendChild(checkoutButton);

        }
    }

    function updateShoppingCartQuantity(productId, quantity) {
        if (quantity < 1) {
            removeProduct(productId);
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
                    // Perbarui quantity input
                    const quantityInput = document.querySelector(`input[name="quantity-${productId}"]`);
                    if (quantityInput) quantityInput.value = quantity;

                    // Perbarui total keseluruhan
                    updateShoppingCartTotal(data.total);

                } else {
                    alert('Gagal memperbarui kuantitas: ' + (data.message || ''));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui kuantitas.');
            });
    }

    function increaseShoppingCartQuantity(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value) + 1;
            const maxStock = parseInt(input.getAttribute('max'));

            if (quantity > maxStock) {
                alert('Kuantitas tidak boleh melebihi stok yang tersedia.');
                return;
            }

            input.value = quantity; // Langsung update nilai input
            updateShoppingCartQuantity(productId, quantity);
            updateCartQuantity(productId, quantity) // Kirim permintaan ke server
        }
    }

    function decreaseShoppingCartQuantity(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value) - 1;

            // Jika quantity kurang dari 1, hapus produk dari keranjang
            if (quantity < 1) {
                removeProduct(productId);
                return;
            }

            input.value = quantity; // Langsung update nilai input
            updateShoppingCartQuantity(productId, quantity);
            updateCartQuantity(productId, quantity) // Kirim permintaan ke server
        }
    }

    function updateShoppingCartQuantityManual(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value);
            const maxStock = parseInt(input.getAttribute('max'));

            // Validasi quantity
            if (isNaN(quantity) || quantity < 1) {
                alert('Kuantitas harus lebih dari 0');
                input.value = 1; // Reset ke nilai default
                return;
            }

            if (quantity > maxStock) {
                alert('Kuantitas tidak boleh melebihi stok yang tersedia.');
                input.value = maxStock; // Reset ke nilai maksimum
                return;
            }

            updateShoppingCartQuantity(productId, quantity);
            updateCartQuantity(productId, quantity)
        }
    }

    function removeFromShoppingCart(productId) {
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
                    const itemElement = document.querySelector(`#shoppingcart-item-${productId}`);
                    if (itemElement) itemElement.remove();

                    // Perbarui total keseluruhan
                    updateShoppingCartTotal(data.total);

                    // Periksa apakah keranjang kosong
                    const cartItems = document.querySelectorAll('[id^="shoppingcart-item-"]');
                    if (cartItems.length === 0) {

                        const shoppingCart = document.querySelector('#shoppingCart');
                        // Tampilkan pesan keranjang kosong

                        const emptyCartMessage = document.createElement('div');
                        emptyCartMessage.id = 'empty-shopping-cart-message';
                        emptyCartMessage.className = 'flex flex-col items-center justify-center';
                        emptyCartMessage.innerHTML =
                            `<img src="${window.imageUrl}" alt="Kucing Cape" class="w-100 mb-4"> <div class = "flex items-center justify-center h-10"><p class = "text-2xl text-gray-500 dark:text-gray-400">Keranjang kosong</p></div >`;

                        shoppingCart.appendChild(emptyCartMessage);

                        // Tampilkan tombol "Kembali Belanja"
                        const backShoppingButton = document.createElement('div');
                        backShoppingButton.id = 'back-shopping';
                        backShoppingButton.className = 'space-y-6';
                        backShoppingButton.innerHTML = `
                    <a href="{{ route('product') }}" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Kembali Belanja</a>
                `;
                        shoppingCart.appendChild(backShoppingButton);

                        const totalShoppingCartContainer = document.querySelector('#shopping-cart-total-container');
                        if (totalShoppingCartContainer) {
                            totalShoppingCartContainer.remove();
                        }

                        const checkoutButton = document.querySelector('#checkout-button');
                        if (checkoutButton) {
                            checkoutButton.remove();
                        }
                    }


                } else {
                    toastr.error('Gagal menghapus produk!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus produk.');
            });
    }

    function updateShoppingCartTotal(total) {
        const totalElement = document.querySelector('#shopping-cart-total');
        if (totalElement) {
            totalElement.textContent = total.toLocaleString();
        }
    }

    function updateSubtotal(productId, quantity) {
        const item = cart[productId];
        const subtotal = item.price * quantity;
        const subtotalElement = document.querySelector(`#subtotal-${productId}`);

        if (subtotalElement) {
            subtotalElement.textContent = subtotal.toLocaleString();
        }
    }

    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp. ' + ribuan;
    }

    // productdetail
    function increaseQuantityProductDetail() {
        const quantityInput = document.getElementById('product-detail-quantity');
        let currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1; // Tambah nilai quantity
    }

    function decreaseQuantityProductDetail() {
        const quantityInput = document.getElementById('product-detail-quantity');
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) { // Pastikan quantity tidak kurang dari 1
            quantityInput.value = currentValue - 1; // Kurangi nilai quantity
        }
    }
</script>

</html>
