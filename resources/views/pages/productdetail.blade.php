@extends('layouts.store')

@section('title', 'Product Details')

@section('content')
    <section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">
        <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
                <div class="shrink-0 max-w-md lg:max-w-lg mx-auto">
                    <!-- Foto utama -->
                    <img id="mainImage" class="w-full dark:hidden"
                        src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg" alt="Main Product Image" />
                    <img id="mainImageDark" class="w-full hidden dark:block"
                        src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg"
                        alt="Main Product Image Dark" />

                    <!-- Daftar foto kecil -->
                    <div class="flex mt-4 space-x-4">
                        <img class="w-20 h-20 cursor-pointer border-2 border-transparent hover:border-gray-300"
                            src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg"
                            onclick="changeImage('https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg', 'https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg')"
                            alt="Thumbnail 1" />
                        <img class="w-20 h-20 cursor-pointer border-2 border-transparent hover:border-gray-300"
                            src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-side.svg"
                            onclick="changeImage('https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-side.svg', 'https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-side-dark.svg')"
                            alt="Thumbnail 2" />
                        <img class="w-20 h-20 cursor-pointer border-2 border-transparent hover:border-gray-300"
                            src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-back.svg"
                            onclick="changeImage('https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-back.svg', 'https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-back-dark.svg')"
                            alt="Thumbnail 3" />
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 lg:mt-0">
                    <!-- Konten deskripsi produk -->
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                        Apple iMac 24" All-In-One Computer, Apple M1, 8GB RAM, 256GB SSD, Mac OS, Pink
                    </h1>
                    <div class="mt-4 sm:items-center sm:gap-4 sm:flex">
                        <p class="text-2xl font-extrabold text-gray-900 sm:text-3xl dark:text-white">
                            $1,249.99
                        </p>
                        {{-- <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <div class="flex items-center gap-1">
                                <!-- Ikon bintang -->
                                <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                </svg>
                                <!-- Ulangi ikon bintang untuk rating -->
                            </div>
                            <p class="text-sm font-medium leading-none text-gray-500 dark:text-gray-400">
                                (5.0)
                            </p>
                            <a href="#"
                                class="text-sm font-medium leading-none text-gray-900 underline hover:no-underline dark:text-white">
                                345 Reviews
                            </a>
                        </div> --}}
                    </div>

                    <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-0">
                        <div class="flex items-center mt-4">
                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-2">
                                <!-- Tombol Minus -->
                                <button onclick="decreaseQuantity()"
                                    class="p-2 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14" />
                                    </svg>
                                </button>

                                <!-- Input Kuantitas -->
                                <input id="quantity" name="quantity" min="1" value="1"
                                    class="w-16 px-3 py-2 text-sm text-center text-gray-900 border border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />

                                <!-- Tombol Plus -->
                                <button onclick="increaseQuantity()"
                                    class="p-2 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14m-7 7V5" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Tombol Add to Cart -->
                            <a href="#" title=""
                                class="flex-1 text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 flex items-center justify-center ml-2"
                                role="button">
                                <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                </svg>
                                Add to cart
                            </a>
                        </div>
                    </div>

                    <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />

                    <p class="mb-6 text-gray-500 dark:text-gray-400">
                        Studio quality three mic array for crystal clear calls and voice recordings. Six-speaker sound
                        system for a remarkably robust and high-quality audio experience. Up to 256GB of ultrafast SSD
                        storage.
                    </p>

                    <p class="text-gray-500 dark:text-gray-400">
                        Two Thunderbolt USB 4 ports and up to two USB 3 ports. Ultrafast Wi-Fi 6 and Bluetooth 5.0 wireless.
                        Color matched Magic Mouse with Magic Keyboard or Magic Keyboard with Touch ID.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
        function changeImage(mainImageSrc, mainImageDarkSrc) {
            document.getElementById('mainImage').src = mainImageSrc;
            document.getElementById('mainImageDark').src = mainImageDarkSrc;
        }

        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value);
            quantityInput.value = currentValue + 1;
        }

        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }
    </script>

    <script>
        function changeImage(mainImageSrc, mainImageDarkSrc) {
            document.getElementById('mainImage').src = mainImageSrc;
            document.getElementById('mainImageDark').src = mainImageDarkSrc;
        }
    </script>
@endsection
