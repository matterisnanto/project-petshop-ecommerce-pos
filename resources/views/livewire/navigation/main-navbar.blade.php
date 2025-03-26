<nav class="bg-white dark:bg-gray-800 antialiased">
    <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
        <div class="flex items-center justify-between">

            <div class="flex items-center space-x-8">
                <div class="shrink-0">
                    <a href="#" title="" class="">
                        <img class="block w-auto h-8 dark:hidden"
                            src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/logo-full.svg" alt="">
                        <img class="hidden w-auto h-8 dark:block"
                            src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/logo-full-dark.svg" alt="">
                    </a>
                </div>

                <ul class="hidden lg:flex items-center justify-start gap-6 md:gap-8 py-3 sm:justify-center">
                    <li>
                        <a wire:navigate href="/" title=""
                            class="{{ request()->is('/') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Home
                        </a>
                    </li>
                    <li class="shrink-0">
                        <a wire:navigate href="/products" title=""
                            class="{{ request()->is('products*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Products
                        </a>
                    </li>
                    <li class="shrink-0">
                        <a wire:navigate href="/trx-check" title=""
                            class="{{ request()->is('trx-check*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Trx Check
                        </a>
                    </li>
                    <li class="shrink-0">
                        <a wire:navigate href="/contact-us" title=""
                            class="{{ request()->is('contact-us') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex items-center lg:space-x-2">

                <!-- Mobile Button -->
                <button type="button"
                    class="sm:hidden flex items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white relative">
                    <!-- Badge Notification -->
                    <span
                        class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -right-1 dark:border-gray-900">
                        3
                    </span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                    </svg>
                </button>

                <!-- Desktop Button -->
                <button id="myCartDropdownButton1" data-dropdown-toggle="myCartDropdown1" type="button"
                    class="hidden sm:flex items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white relative">
                    <span class="sr-only">Cart</span>
                    <!-- Badge Notification -->
                    <span
                        class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -right-1 dark:border-gray-900">
                        3
                    </span>
                    <svg class="w-5 h-5 lg:me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                    </svg>
                    <span class="hidden sm:inline-flex items-center">
                        My Cart
                    </span>
                </button>

                <div id="myCartDropdown1"
                    class="hidden z-10 mx-auto max-w-lg rounded-lg bg-white p-5 shadow-sm border border-gray-200 dark:bg-gray-800 right-0 left-0">
                    <div class="space-y-2">
                        <!-- Product on cart -->
                        <div class="flex items-center gap-4 p-1">
                            <img src="{{ asset('img/b2f610d5d162031e280ec4c61b13e98a.jpg') }}" alt="Apple iPhone 15"
                                class="w-16 h-16 rounded-lg shadow-md">
                            <div class="flex-1 min-w-0">
                                <a href="#"
                                    class="block text-sm font-medium text-gray-900 dark:text-white hover:underline truncate max-w-[150px]">
                                    Apple iPhone 15 Pro
                                </a>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">$599</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-red-400 rounded-lg hover:bg-red-500 focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 13H5v-2h14v2z" />
                                    </svg>
                                </button>
                                <input
                                    class="w-10 p-1 text-center text-sm font-medium border-gray-500 border rounded-lg dark:bg-gray-700 dark:text-white"
                                    value="1" min="1">
                                <button type="button"
                                    class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-400 rounded-lg hover:bg-green-500 focus:ring-4 focus:ring-green-300 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6v-2z" />
                                    </svg>
                                </button>
                                <button type="button"
                                    class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                    <span class="sr-only">Remove</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- End product on cart -->

                        <div class="flex justify-between text-base font-medium border-gray-400 border-t pt-1">
                            <span class="text-gray-900 dark:text-white">Total:</span>
                            <span class="text-gray-900 dark:text-white" id="cartTotal">$599</span>
                        </div>
                    </div>

                    <a href="#"
                        class="mt-4 block w-full rounded-lg bg-primary-500 px-5 py-2 text-center text-sm font-medium text-white shadow-md hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                        View your cart
                    </a>
                </div>





                {{-- <button id="userDropdownButton1" data-dropdown-toggle="userDropdown1" type="button"
                    class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                    <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-width="2"
                            d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Account
                    <svg class="w-4 h-4 text-gray-900 dark:text-white ms-1" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
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
            <ul class="text-gray-900 dark:text-white text-sm font-medium dark:text-white space-y-3">
                <li>
                    <a wire:navigate href="/"
                        class="{{ request()->is('/') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Home</a>
                </li>
                <li>
                    <a wire:navigate href="/products"
                        class="{{ request()->is('products*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Products</a>
                </li>
                <li>
                    <a wire:navigate href="/trx-check*"
                        class="{{ request()->is('trx-check') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Trx
                        Check</a>
                </li>
                <li>
                    <a wire:navigate href="/contact-us"
                        class="{{ request()->is('contact-us') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Contact
                        Us</a>
                </li>
                </li>
            </ul>
        </div>
    </div>
</nav>
