@extends('layouts.store')

@section('title', 'Product Details')

@section('content')
    <section class="py-2 bg-white md:py-16 dark:bg-gray-900 antialiased">

        <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0">
            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                <div class="mb-4 sm:mb-0"> <!-- Tambahkan margin-bottom untuk mobile -->
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                            <li class="inline-flex items-center">
                                <a href="{{ route('product') }}"
                                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                                    <svg class="me-2.5 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                                    </svg>
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m9 5 7 7-7 7" />
                                    </svg>
                                    <a href="{{ route('product') }}"
                                        class="ms-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white md:ms-2 truncate max-w-[150px] md:max-w-none">
                                        {{ $product->category->name }}
                                    </a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m9 5 7 7-7 7" />
                                    </svg>
                                    <span
                                        class="ms-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ms-2 truncate max-w-[100px] md:max-w-none">
                                        {{ $product->name }}
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
                <div class="shrink-0 max-w-md lg:max-w-lg mx-auto mt-6 sm:mt-0">
                    <!-- Container untuk foto utama -->
                    <div class="w-full aspect-[4/3] overflow-hidden flex items-center justify-center">
                        <!-- Foto utama -->
                        <img id="mainImage" class="w-full h-full object-contain dark:hidden" src="{{ $product->image_url }}"
                            alt="Main Product Image" />
                        <img id="mainImageDark" class="w-full h-full object-contain hidden dark:block"
                            src="{{ $product->image_url }}" alt="Main Product Image Dark" />
                    </div>

                    <!-- Daftar foto kecil -->
                    <div class="flex mt-4 space-x-4">
                        <img class="w-20 h-20 cursor-pointer border-2  border-gray-200 sm:border-gray-200 sm:hover:border-primary-300 "
                            src="{{ $product->image_url }}"
                            onclick="changeImage('{{ $product->image_url }}', '{{ $product->image_url }}')"
                            alt="Thumbnail 1" />
                        @foreach ($product->photos as $photo)
                            <img class="w-20 h-20 cursor-pointer border-2  border-gray-200 sm:border-gray-200 sm:hover:border-primary-300 "
                                src="{{ asset('storage/' . $photo->photo) }}"
                                onclick="changeImage('{{ asset('storage/' . $photo->photo) }}', '{{ asset('storage/' . $photo->photo) }}')"
                                alt="Thumbnail {{ $loop->iteration + 1 }}" />
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 lg:mt-0">
                    <!-- Konten deskripsi produk -->
                    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">
                        {{ $product->name }}
                    </h1>
                    <p class="font-semibold text-gray-500 dark:text-gray-400">{{ $product->barcode }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Brand : {{ $product->brand->name }}</p>
                    <div class="mt-4 sm:items-center sm:gap-4 sm:flex">
                        <p class="text-2xl font-extrabold text-gray-900 sm:text-3xl dark:text-white">
                            Rp. {{ number_format($product->selling_price, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-0">
                        <div class="flex items-center mt-4">
                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-2">
                                <!-- Tombol Minus -->
                                <button onclick="decreaseQuantityProductDetail()"
                                    class="p-2 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14" />
                                    </svg>
                                </button>

                                <!-- Input Kuantitas -->
                                <input id="product-detail-quantity" name="product-detail-quantity" min="1"
                                    value="1"
                                    class="w-16 px-3 py-2 text-sm text-center text-gray-900 border border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />

                                <!-- Tombol Plus -->
                                <button onclick="increaseQuantityProductDetail()"
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
                                role="button" onclick="addToCart({{ $product->id }})">
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

                    <hr class="my-6 md:my-8 border-gray-500 dark:border-gray-800" />

                    <p class="text-gray-500 dark:text-gray-400">
                        {{ $product->about }}
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
    </script>
@endsection
