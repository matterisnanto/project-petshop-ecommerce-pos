<div>
    <section class="py-2 bg-white md:py-4 dark:bg-gray-900 antialiased">
        <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0">
            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                <div class="mb-4 sm:mb-0">
                    <livewire:navigation.bread-crumb :links="[
                        ['text' => 'Home', 'url' => route('home')],
                        [
                            'text' => $product->category->name,
                            'url' => route('products', $product->category->slug),
                        ],
                    ]" :currentPage="$product->name" />
                </div>
            </div>

            <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
                <div class="shrink-0 max-w-md lg:max-w-lg mx-auto mt-6 sm:mt-0">
                    <!-- Main Image Container -->
                    <div
                        class="w-full aspect-[4/3] overflow-hidden flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <img id="mainImage"
                            class="w-full h-full object-contain transition-opacity duration-300 dark:hidden"
                            src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" />
                        <img id="mainImageDark"
                            class="w-full h-full object-contain transition-opacity duration-300 hidden dark:block"
                            src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" />
                    </div>

                    <!-- Thumbnail Gallery -->
                    <div class="flex mt-4 space-x-3 overflow-x-auto py-2">
                        <!-- Main Thumbnail -->
                        <div class="flex-shrink-0 relative">
                            <img class="w-20 h-20 rounded-md cursor-pointer object-cover border-2 border-primary-500"
                                src="{{ $product->image_url }}" onclick="changeMainImage('{{ $product->image_url }}')"
                                alt="{{ $product->name }}" title="Main Image" />
                            <span
                                class="absolute top-0 left-0 bg-primary-500 text-white text-xs px-1 rounded-br-md">Main</span>
                        </div>

                        <!-- Additional Photos -->
                        @foreach ($product->photos as $photo)
                            <img class="flex-shrink-0 w-20 h-20 rounded-md cursor-pointer object-cover border-2 border-gray-200 hover:border-primary-300"
                                src="{{ $photo->photo_url }}" onclick="changeMainImage('{{ $photo->photo_url }}')"
                                alt="{{ $product->name }} - Photo {{ $loop->iteration }}"
                                title="Photo {{ $loop->iteration }}" />
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 lg:mt-0">
                    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">
                        {{ $product->name }}
                    </h1>
                    <p class="font-semibold text-gray-500 dark:text-gray-400">{{ $product->barcode }}</p>
                    <p class="text-gray-500 dark:text-gray-400"> {{ $product->category->name }} -
                        {{ $product->brand->name }}</p>
                    <div class="mt-2 sm:items-center sm:gap-4 sm:flex">
                        <p class="text-2xl font-extrabold text-gray-900 sm:text-3xl dark:text-white">
                            {{ format_currency($product->selling_price) }}
                        </p>
                    </div>

                    <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-0">
                        <div class="flex items-center mt-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="decrementQuantity"
                                    class="p-2 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14" />
                                    </svg>
                                </button>

                                <input wire:model="quantity" min="1" max="{{ $product->stock }}"
                                    class="w-16 px-3 py-2 text-sm text-center text-gray-900 border border-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />

                                <button wire:click="incrementQuantity"
                                    class="p-2 text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14m-7 7V5" />
                                    </svg>
                                </button>
                            </div>

                            <button wire:click="addToCart"
                                class="flex-1 text-white bg-primary-500 hover:bg-primary-600 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 flex items-center justify-center ml-2">
                                <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                </svg>
                                Add to cart
                            </button>
                            {{-- <p class="ml-4">Stok : {{ $product->stock }}</p> --}}
                        </div>
                    </div>

                    <hr class="my-4 md:my-4 border-gray-500 dark:border-gray-800" />
                    <div class="lg:text-left text-center">
                        <p class="font-semibold text-center">atau kunjungi toko kami di</p>
                        <div class="flex items-center justify-center lg:justify-start gap-2 mt-2">
                            <a href="#" title=""
                                class="flex-1 text-white bg-orange-500 hover:bg-orange-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 flex items-center justify-center ml-2"
                                role="button" onclick="addToCart()">
                                <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    x="0px" y="0px" width="100" height="100" viewBox="0,0,256,256">
                                    <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1"
                                        stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10"
                                        stroke-dasharray="" stroke-dashoffset="0" font-family="none"
                                        font-weight="none" font-size="none" text-anchor="none"
                                        style="mix-blend-mode: normal">
                                        <g transform="scale(5.12,5.12)">
                                            <path
                                                d="M25,1c-5.32713,0 -9.39588,4.95314 -9.83398,11h-10.10742c-1.135,0 -2.05922,0.981 -1.99609,2.11328l1.72461,30.17188c0.14947,2.63699 2.34979,4.71484 4.99023,4.71484h30.44531c2.64119,0 4.84078,-2.07817 4.99023,-4.71484l1.72461,-30.16992c0.06514,-1.13309 -0.86109,-2.11523 -1.99609,-2.11523h-10.10742c-0.43811,-6.04686 -4.50685,-11 -9.83398,-11zM25,3c4.03694,0 7.40892,3.88679 7.83594,9h-15.67188c0.42701,-5.11321 3.799,-9 7.83594,-9zM25.08008,18c3.84,0 6.71898,2.06094 6.95898,2.21094l-1.0293,1.66992c-0.25,-0.17 -0.69914,-0.44102 -0.86914,-0.54102c-1.02,-0.55 -2.90055,-1.36914 -5.06055,-1.36914c-3.25,0 -5.59961,1.7993 -5.59961,4.2793c0,2.47 2.17976,3.44977 5.75977,4.75977c3.63,1.32 7.73047,2.81984 7.73047,7.58984c0,3.59 -3.54055,6.41016 -8.06055,6.41016c-3.99,0 -7.27008,-2.44961 -8.08008,-3.09961l1.10938,-1.65039c0.03,0.03 3.2207,2.76953 6.9707,2.76953c3.36,0 6.08984,-1.98969 6.08984,-4.42969c0,-3.16 -2.42945,-4.28023 -6.43945,-5.74023c-3.3,-1.21 -7.05078,-2.57937 -7.05078,-6.60937c0,-3.56 3.25031,-6.25 7.57031,-6.25z">
                                            </path>
                                        </g>
                                    </g>
                                </svg>
                                Shopee
                            </a>
                            <a href="#" title=""
                                class="flex-1 text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 flex items-center justify-center ml-2"
                                role="button" onclick="addToCart()">
                                <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    x="0px" y="0px" width="100" height="100" viewBox="0,0,256,256">
                                    <defs>
                                        <linearGradient x1="32.135" y1="1.445" x2="32.135" y2="51.043"
                                            gradientUnits="userSpaceOnUse" id="color-1_QxTCUohbBw9U_gr1">
                                            <stop offset="0" stop-color="#d9f7ee"></stop>
                                            <stop offset="0.492" stop-color="#2df19b"></stop>
                                            <stop offset="1" stop-color="#e6abff"></stop>
                                        </linearGradient>
                                        <linearGradient x1="23.522" y1="-3.418" x2="23.522" y2="63.822"
                                            gradientUnits="userSpaceOnUse" id="color-2_QxTCUohbBw9U_gr2">
                                            <stop offset="0" stop-color="#7de3c3"></stop>
                                            <stop offset="1" stop-color="#0ba360"></stop>
                                        </linearGradient>
                                        <linearGradient x1="32.135" y1="-3.418" x2="32.135" y2="63.822"
                                            gradientUnits="userSpaceOnUse" id="color-3_QxTCUohbBw9U_gr3">
                                            <stop offset="0" stop-color="#7de3c3"></stop>
                                            <stop offset="1" stop-color="#0ba360"></stop>
                                        </linearGradient>
                                        <linearGradient x1="40.749" y1="-3.418" x2="40.749" y2="63.822"
                                            gradientUnits="userSpaceOnUse" id="color-4_QxTCUohbBw9U_gr4">
                                            <stop offset="0" stop-color="#7de3c3"></stop>
                                            <stop offset="1" stop-color="#0ba360"></stop>
                                        </linearGradient>
                                        <linearGradient x1="31.85" y1="37.11" x2="31.85" y2="43.98"
                                            gradientUnits="userSpaceOnUse" id="color-5_QxTCUohbBw9U_gr5">
                                            <stop offset="0" stop-color="#d9f7ee"></stop>
                                            <stop offset="0.492" stop-color="#2df19b"></stop>
                                            <stop offset="1" stop-color="#e6abff"></stop>
                                        </linearGradient>
                                        <linearGradient x1="31.846" y1="-4.535" x2="31.846" y2="62.706"
                                            gradientUnits="userSpaceOnUse" id="color-6_QxTCUohbBw9U_gr6">
                                            <stop offset="0" stop-color="#7de3c3"></stop>
                                            <stop offset="1" stop-color="#0ba360"></stop>
                                        </linearGradient>
                                        <linearGradient x1="32.135" y1="-3.418" x2="32.135" y2="63.822"
                                            gradientUnits="userSpaceOnUse" id="color-7_QxTCUohbBw9U_gr7">
                                            <stop offset="0" stop-color="#7de3c3"></stop>
                                            <stop offset="1" stop-color="#0ba360"></stop>
                                        </linearGradient>
                                    </defs>
                                    <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1"
                                        stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10"
                                        stroke-dasharray="" stroke-dashoffset="0" font-family="none"
                                        font-weight="none" font-size="none" text-anchor="none"
                                        style="mix-blend-mode: normal">
                                        <g transform="scale(4,4)">
                                            <path
                                                d="M54,13.6v24.51c0,8.79 -7.12,15.91 -15.9,15.91h-27.83v-40.42h12.59c2.93,0 6.62,1.99 9.28,4.64c2.65,-2.65 6.34,-4.64 9.27,-4.64z"
                                                fill="url(#color-1_QxTCUohbBw9U_gr1)"></path>
                                            <circle cx="22.859" cy="30.163" r="9.276" fill="#ffffff"></circle>
                                            <circle cx="41.411" cy="30.163" r="9.276" fill="#ffffff"></circle>
                                            <path
                                                d="M44,48.473c0,0.799 -0.109,2.78 -0.298,3.527h-23.134c-0.189,-0.746 -0.298,-2.728 -0.298,-3.527c0,-5.785 5.313,-10.473 11.87,-10.473c6.547,0 11.86,4.688 11.86,10.473z"
                                                fill="#ffffff"></path>
                                            <circle cx="23.522" cy="30.825" r="4.638"
                                                fill="url(#color-2_QxTCUohbBw9U_gr2)"></circle>
                                            <circle cx="21.203" cy="27.181" r="2.982" fill="#ffffff"></circle>
                                            <path
                                                d="M41.41,14.6c-2.53,0 -5.97,1.74 -8.57,4.34c-0.19,0.2 -0.45,0.3 -0.7,0.3c-0.26,0 -0.52,-0.1 -0.71,-0.3c-2.6,-2.6 -6.04,-4.34 -8.57,-4.34h-11.59v38.42h26.83c8.21,0 14.9,-6.69 14.9,-14.91v-23.51zM51,38.11c0,7.12 -5.79,12.91 -12.9,12.91h-24.83v-34.42h9.59c1.69,0 4.69,1.29 7.15,3.76c0.57,0.56 1.32,0.88 2.13,0.88c0.8,0 1.55,-0.32 2.12,-0.88c2.46,-2.47 5.46,-3.76 7.15,-3.76h9.59z"
                                                fill="#ffffff"></path>
                                            <path
                                                d="M41.067,20.23c-3.929,0 -7.322,2.299 -8.932,5.617c-1.61,-3.318 -5.003,-5.617 -8.933,-5.617c-5.477,0 -9.932,4.455 -9.932,9.932c0,5.477 4.456,9.933 9.932,9.933c3.929,0 7.323,-2.299 8.933,-5.618c1.61,3.318 5.003,5.618 8.932,5.618c5.477,0 9.933,-4.456 9.933,-9.933c0,-5.477 -4.456,-9.932 -9.933,-9.932zM23.203,38.095c-4.374,0 -7.932,-3.559 -7.932,-7.933c0,-4.373 3.558,-7.932 7.932,-7.932c4.374,0 7.933,3.559 7.933,7.932c-0.001,4.374 -3.559,7.933 -7.933,7.933zM41.067,38.095c-4.374,0 -7.932,-3.559 -7.932,-7.933c0,-4.373 3.558,-7.932 7.932,-7.932c4.374,0 7.933,3.559 7.933,7.932c0,4.374 -3.559,7.933 -7.933,7.933z"
                                                fill="url(#color-3_QxTCUohbBw9U_gr3)"></path>
                                            <circle cx="40.749" cy="30.825" r="4.638"
                                                fill="url(#color-4_QxTCUohbBw9U_gr4)"></circle>
                                            <circle cx="38.43" cy="27.181" r="2.982" fill="#ffffff"></circle>
                                            <path
                                                d="M36.57,39.3l-4.43,4.44l-0.24,0.24l-4.77,-4.77c1.14,-1.29 2.82,-2.1 4.67,-2.1c0.12,0 0.22,0.02 0.34,0.02c1.77,0.09 3.34,0.91 4.43,2.17z"
                                                fill="url(#color-5_QxTCUohbBw9U_gr5)"></path>
                                            <path
                                                d="M31.9,45.278l-6.142,-6.143l0.623,-0.704c1.369,-1.549 3.344,-2.438 5.419,-2.438c0.091,0 0.175,0.006 0.258,0.014l0.133,0.007c1.997,0.102 3.82,0.994 5.135,2.515l0.608,0.703zM28.562,39.112l3.337,3.337l3.25,-3.258c-0.865,-0.709 -1.924,-1.121 -3.061,-1.179c-0.035,0.004 -0.123,-0.005 -0.208,-0.013c-0.007,0 -0.015,0 -0.021,0c-1.24,0.001 -2.385,0.399 -3.297,1.113z"
                                                fill="url(#color-6_QxTCUohbBw9U_gr6)"></path>
                                            <path
                                                d="M54,12.6h-11.736c-0.798,-4.877 -5.03,-8.616 -10.129,-8.616c-5.099,0 -9.331,3.739 -10.129,8.616h-11.736c-0.55,0 -1,0.45 -1,1v40.42c0,0.55 0.45,1 1,1h27.83c9.32,0 16.9,-7.59 16.9,-16.91v-24.51c0,-0.55 -0.45,-1 -1,-1zM32.135,5.984c4.025,0 7.384,2.89 8.122,6.703c-2.603,0.367 -5.616,1.906 -8.117,4.172c-2.51,-2.266 -5.523,-3.805 -8.126,-4.172c0.737,-3.812 4.096,-6.703 8.121,-6.703zM53,38.11c0,8.22 -6.69,14.91 -14.9,14.91h-26.83v-38.42h11.59c2.53,0 5.97,1.74 8.57,4.34c0.19,0.2 0.45,0.3 0.71,0.3c0.25,0 0.51,-0.1 0.7,-0.3c2.6,-2.6 6.04,-4.34 8.57,-4.34h11.59z"
                                                fill="url(#color-7_QxTCUohbBw9U_gr7)"></path>
                                        </g>
                                    </g>
                                </svg>
                                Tokopedia
                            </a>
                        </div>
                    </div>
                    <hr class="my-6 md:my-4 border-gray-500 dark:border-gray-800" />

                    <p class="text-gray-500 dark:text-gray-400">
                        {{ $product->description }}
                    </p>
                </div>
            </div>
        </div>
    </section>
    <script>
        function changeMainImage(newSrc) {
            // Update both light and dark mode images
            document.getElementById('mainImage').src = newSrc;
            document.getElementById('mainImageDark').src = newSrc;

            // Add smooth transition effect
            const images = [document.getElementById('mainImage'), document.getElementById('mainImageDark')];
            images.forEach(img => {
                img.style.opacity = '0';
                setTimeout(() => {
                    img.src = newSrc;
                    img.style.opacity = '1';
                }, 150);
            });

            // Update active thumbnail border
            document.querySelectorAll('[onclick^="changeMainImage"]').forEach(el => {
                el.classList.remove('border-primary-500');
                el.classList.add('border-gray-200');
            });
            event.target.classList.add('border-primary-500');
            event.target.classList.remove('border-gray-200');
        }
    </script>
</div>
