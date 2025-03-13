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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update quantity input
                    const quantityInput = document.querySelector(`input[name="quantity-${productId}"]`);
                    quantityInput.value = quantity;

                    // Update total keseluruhan
                    updateCartTotal(data.total);
                } else {
                    alert('Gagal memperbarui kuantitas!');
                }
            });
    }

    function updateQuantityManual(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        let quantity = parseInt(input.value);

        // Jika quantity kurang dari 1, hapus produk dari keranjang
        if (quantity < 1) {
            removeFromCart(productId);
            return;
        }

        // Kirim permintaan update ke server
        updateQuantity(productId, quantity);
    }

    function increaseQuantity(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        let quantity = parseInt(input.value) + 1;
        input.value = quantity;
        updateQuantity(productId, quantity);
    }

    function decreaseQuantity(productId) {
        const input = document.querySelector(`input[name="quantity-${productId}"]`);
        let quantity = parseInt(input.value) - 1;

        // Jika quantity kurang dari 1, hapus produk dari keranjang
        if (quantity < 1) {
            removeFromCart(productId);
            return;
        }

        input.value = quantity;
        updateQuantity(productId, quantity);
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
                        emptyCartMessage.className = 'flex items-center justify-center h-10';
                        emptyCartMessage.innerHTML =
                            '<p class="text-gray-500 dark:text-gray-400">Keranjang kosong</p>';

                        const cartDropdown = document.querySelector('#myCartDropdown1');
                        cartDropdown.insertBefore(emptyCartMessage, cartDropdown.firstChild);

                        // Sembunyikan atau hapus elemen total
                        const totalContainer = document.querySelector('#cart-total-container');
                        if (totalContainer) {
                            totalContainer.remove(); // atau totalContainer.style.display = 'none';
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
