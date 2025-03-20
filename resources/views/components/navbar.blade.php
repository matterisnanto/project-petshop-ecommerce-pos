<nav class="bg-white dark:bg-gray-800 antialiased">
    <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
        <div class="flex items-center justify-between">

            <div class="flex items-center space-x-8">
                <div class="shrink-0">
                    <a href="#" title="" class="">
                        {{-- <img class="block w-auto h-8 dark:hidden"
                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/logo-full.svg" alt="">
                            <img class="hidden w-auto h-8 dark:block"
                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/logo-full-dark.svg"
                                alt=""> --}}
                        <b class="text-2xl">°Petshop</b>
                    </a>
                </div>

                <ul class="hidden lg:flex items-center justify-start gap-6 md:gap-8 py-3 sm:justify-center">
                    <li>
                        <a href="{{ url('/') }}" title="Home"
                            class="text-sm font-medium {{ request()->is('/') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">
                            Home
                        </a>
                    </li>
                    <li class="shrink-0">
                        <a href="{{ route('product') }}" title=""
                            class="text-sm font-medium {{ request()->is('product*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">
                            Product
                        </a>
                    </li>
                    <li class="shrink-0">
                        <a href="{{ route('trx') }}" title=""
                            class="text-sm font-medium {{ request()->is('trx-check') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">
                            Trx check
                        </a>
                    </li>
                    <li class="shrink-0 ">
                        <a href="{{ route('contactus') }}" title=""
                            class="text-sm font-medium {{ request()->is('contact-us') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex items-center lg:space-x-2">
                <!-- Tombol Cart -->
                <button id="myCartDropdownButton1" data-dropdown-toggle="myCartDropdown1" type="button"
                    class="inline-flex items-center rounded-lg justify-center p-2 {{ request()->is('shopping-cart') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none">
                    <span class="sr-only">Cart</span>
                    <svg class="w-5 h-5 lg:me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                    </svg>
                    <span class="hidden sm:flex">My Cart</span>
                    <svg class="hidden sm:flex w-4 h-4{{ request()->is('shopping-cart') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} ms-1"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Cart -->
                <div id="myCartDropdown1"
                    class="absolute z-1000 right-10 mt-2 w-96 bg-white border rounded-lg shadow-lg border-gray-200 dark:bg-gray-800 hidden">
                    @if (count(session('cart', [])) > 0)
                        @foreach (session('cart') as $id => $item)
                            <!-- Produk -->
                            <div id="cart-item-{{ $id }}"
                                class="grid grid-cols-2 p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="max-w-[200px]">
                                    <a href="#"
                                        class="block truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">
                                        {{ $item['name'] }}
                                    </a>
                                    <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">
                                        Rp. {{ number_format($item['price'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-end gap-4">
                                    <div class="flex items-center gap-2">
                                        <!-- Tombol Minus -->
                                        <button onclick="decreaseQuantityCart({{ $id }})"
                                            class="p-1.5 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                            </svg>
                                        </button>
                                        <!-- Input Kuantitas -->
                                        <input id="cart-quantity-{{ $id }}"
                                            name="cart-quantity-{{ $id }}" min="1"
                                            value="{{ $item['quantity'] }}"
                                            onchange="updateQuantityManual({{ $id }})"
                                            class="w-12 px-2 py-1.5 text-sm text-center text-gray-900 border border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                                        <!-- Tombol Plus -->
                                        <button onclick="increaseQuantityCart({{ $id }})"
                                            class="p-1.5 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Tombol Hapus -->
                                    <button onclick="removeFromCart({{ $id }})" type="button"
                                        class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                        <span class="sr-only">Remove</span>
                                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <!-- Total Keseluruhan -->
                        <div id="cart-total-container" class="p-2 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-lg font-bold text-gray-900 dark:text-white text-right">
                                Total: Rp. <span
                                    id="cart-total">{{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], session('cart'))), 0, ',', '.') }}</span>
                            </p>
                        </div>

                        <!-- Tombol Lihat Keranjang -->
                        <div id="view-cart-button-container" class="p-4">
                            <a href="{{ route('cart.view') }}" title=""
                                class="w-full inline-flex items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                role="button">Lihat Keranjang</a>
                        </div>
                    @else
                        <!-- Jika Keranjang Kosong -->
                        <div id="empty-cart-message" class="flex items-center justify-center h-20">
                            <p class="text-gray-500 dark:text-gray-400">Keranjang kosong</p>
                        </div>
                    @endif


                </div>

                {{-- <button id="userDropdownButton1" data-dropdown-toggle="userDropdown1" type="button"
                        class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                        <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2"
                                d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Account
                        <svg class="w-4 h-4 text-gray-900 dark:text-white ms-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="userDropdown1"
                        class="hidden z-10 w-56 divide-y divide-gray-100 overflow-hidden overflow-y-auto rounded-lg bg-white antialiased shadow dark:divide-gray-600 dark:bg-gray-700">
                        <ul class="p-2 text-start text-sm font-medium text-gray-900 dark:text-white">
                            <li><a href="#" title=""
                                    class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    My Account </a></li>
                            <li><a href="#" title=""
                                    class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    My Orders </a></li>
                            <li><a href="#" title=""
                                    class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Settings </a></li>
                            <li><a href="#" title=""
                                    class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Favourites </a></li>
                            <li><a href="#" title=""
                                    class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Delivery Addresses </a></li>
                            <li><a href="#" title=""
                                    class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Billing Data </a></li>
                        </ul>

                        <div class="p-2 text-sm font-medium text-gray-900 dark:text-white">
                            <a href="#" title=""
                                class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                Sign Out </a>
                        </div>
                    </div> --}}

                <button type="button" data-collapse-toggle="ecommerce-navbar-menu-1"
                    aria-controls="ecommerce-navbar-menu-1" aria-expanded="false"
                    class="inline-flex lg:hidden items-center justify-center hover:bg-gray-100 rounded-md dark:hover:bg-gray-700 p-2 text-gray-900 dark:text-white">
                    <span class="sr-only">
                        Open Menu
                    </span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M5 7h14M5 12h14M5 17h14" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="ecommerce-navbar-menu-1"
            class="bg-gray-50 dark:bg-gray-700 dark:border-gray-600 border border-gray-200 rounded-lg py-3 hidden px-4 mt-4">
            <ul class="  text-sm font-medium dark:text-white space-y-3">
                <li>
                    <a href="{{ route('home') }}"
                        class="hover:text-primary-700 dark:hover:text-primary-500 {{ request()->is('/') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }}">Home</a>
                </li>
                <li>
                    <a href="{{ route('product') }}"
                        class="hover:text-primary-700 dark:hover:text-primary-500 {{ request()->is('product') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }}">Product</a>
                </li>
                <li>
                    <a href="{{ route('trx') }}"
                        class="hover:text-primary-700 dark:hover:text-primary-500 {{ request()->is('trx-check') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }}">Trx
                        Check</a>
                </li>
                <li>
                    <a href="{{ route('contactus') }}"
                        class="hover:text-primary-700 dark:hover:text-primary-500 {{ request()->is('contact-us') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }}">Contac
                        Us</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
