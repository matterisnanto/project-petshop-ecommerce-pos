<nav class="bg-white dark:bg-gray-800 antialiased">
    <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
        <div class="flex items-center justify-between">

            <div class="flex items-center space-x-8">
                <div class="shrink-0">
                    <a wire:navigate href="/" title="" class="">
                        <img class="block w-auto h-10 dark:hidden" src="{{ asset('images\logo\Logonameblack.png') }}"
                            alt="">
                        <img class="hidden w-auto h-10 dark:block" src="{{ asset('images\logo\Logonamewhite.png') }}"
                            alt="">
                        {{-- <h1>CindyPetshop</h1> --}}
                    </a>
                </div>

                <ul class="hidden lg:flex items-center justify-start gap-6 md:gap-8 py-3 sm:justify-center">
                    <li>
                        <a wire:navigate href="/" title=""
                            class="{{ $activeRoute === '/' ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Home
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/animals" title=""
                            class="{{ str_starts_with($activeRoute, 'animals') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Animals
                        </a>
                    </li>
                    <li class="shrink-0">
                        <a wire:navigate href="/products" title=""
                            class="{{ str_starts_with($activeRoute, 'products') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Products
                        </a>
                    </li>
                    <li>
                        <button type="button" id="dropdown-button" data-dropdown-toggle="dropdown"
                            class="{{ in_array($activeRoute, ['pet-grooming', 'pet-hotel', 'breeding']) ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium hover:text-primary-700 dark:hover:text-primary-500">
                            Service
                            <svg class="pt-1 w-5 h-5 lg:w-4 lg:h-4" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg></button>
                        <div id="dropdown"
                            class="hidden z-10 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700">
                            <ul class="py-1 text-sm font-light text-gray-500 dark:text-gray-400"
                                aria-labelledby="dropdown-button">
                                <li>
                                    <a wire:navigate href="/pet-grooming"
                                        class="{{ $activeRoute === 'pet-grooming' ? 'text-primary-500' : 'text-gray-500 dark:text-white' }} flex items-center py-2 px-4 w-full hover:text-primary-600 dark:hover:text-primary-500">
                                        <svg class="mr-2 w-4 h-4" fill="currentColor" version="1.1" id="Capa_1"
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 98.538 98.538"
                                            xml:space="preserve">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <g>
                                                        <circle cx="66.465" cy="10.188" r="10.188"></circle>
                                                        <path
                                                            d="M69.003,20.967l-2.538,2.555l-2.46-2.584c-0.008,0.004-0.017,0.003-0.024,0.006c-1.338,0.468-4.047,1.713-4.047,1.713 c-8.075,3.457-9.315,3.99-15.829-2.211c-1.289-1.229-3.184-1.399-4.659-0.569l-7.927-6.787c-0.472-0.404-1.182-0.35-1.586,0.123 c-0.327,0.381-0.34,0.911-0.087,1.317l-2.226,2.599c-0.247,0.289-0.214,0.723,0.075,0.971c0.289,0.246,0.723,0.213,0.969-0.076 l2.182-2.548l0.854,0.731l-2.182,2.548c-0.247,0.289-0.213,0.724,0.075,0.97c0.288,0.247,0.722,0.214,0.969-0.074l2.182-2.549 l0.855,0.731l-2.182,2.549c-0.247,0.288-0.213,0.722,0.075,0.969c0.289,0.248,0.722,0.214,0.969-0.075l2.182-2.548l0.855,0.731 l-2.182,2.549c-0.247,0.289-0.214,0.722,0.075,0.969s0.722,0.214,0.969-0.075l2.182-2.548l1.344,1.151 c-0.791,1.52-0.541,3.429,0.763,4.67c5.341,5.086,9.26,6.959,13.249,6.959c1.352,0,2.715-0.223,4.14-0.602v19.776 c0,1.236,0.279,2.375,0.759,3.402c-0.006,0.096-0.003,6.572-0.003,6.572h15.626v8.771h-4.344v26.024 c0.853,0.896,2.047,1.459,3.38,1.459c2.581,0,4.674-2.095,4.674-4.675l0.004-38.147c0.496-1.043,0.76-2.149,0.76-3.407V41.321 c0,0,1.672,3.406,0.813,12.25c-0.211,2.175,1.457,4.08,3.635,4.252c0.105,0.009,0.21,0.013,0.313,0.013 c2.044,0,3.776-1.573,3.938-3.646C87.15,34.266,78.564,23.883,69.003,20.967z M66.49,43.684h-0.048l-2.457-3.363l2.457-16.42 h0.048l2.457,16.42L66.49,43.684z">
                                                        </path>
                                                        <path
                                                            d="M56.772,93.866c0,2.151,1.463,3.944,3.441,4.487V71.055h-3.443L56.772,93.866z">
                                                        </path>
                                                        <polygon
                                                            points="70.919,63.745 13.533,63.745 13.533,69.516 17.876,69.516 17.876,97.943 22.738,97.943 22.738,69.516 61.714,69.516 61.714,97.943 66.576,97.943 66.576,69.516 70.919,69.516 ">
                                                        </polygon>
                                                        <path
                                                            d="M17.374,53.447c0,0.044-3.392,6.683-3.392,6.683c-0.508,0.978-0.128,2.179,0.847,2.687 c0.294,0.154,0.608,0.227,0.917,0.227c0.719,0,1.413-0.391,1.768-1.072l3.427-6.586l3.835,6.66 c0.369,0.639,1.038,0.998,1.728,0.998c0.337,0,0.678-0.086,0.992-0.266c0.953-0.549,1.281-1.768,0.732-2.721l-3.346-5.812h12.393 v6.806c0,1.1,0.892,1.991,1.992,1.991s1.991-0.894,1.991-1.991V53.52c0.002-0.025,0.014-0.045,0.014-0.07v-6.297l-9.275-5.318 H19.791L16,38.789c-0.858-0.689-2.112-0.553-2.8,0.305c-0.688,0.857-0.552,2.111,0.305,2.801l3.868,3.107L17.374,53.447 L17.374,53.447z">
                                                        </path>
                                                        <path
                                                            d="M41.598,29.166c-0.33-0.244-1.959-1.033-2.668,1.668c-0.154,0.586-0.364,1.17-0.644,1.707 c-1.024,1.967-2.324,3.766-3.568,5.598c-0.568,0.834-1.136,1.67-1.702,2.504l8.708,4.959c0,0,1.679-1.836,1.915-1.732 c0.326,0.145,0.655,0.285,0.983,0.428c0.958,0.416,1.958,0.611,3.006,0.549c1.78-0.104,3.635-1.049,3.805-3.184 c0.155-1.957-4.593-3.486-5.008-3.877c-1.231-1.16-1.644-2.518-3.026-3.502c-0.241-0.17-0.485-0.344-0.741-0.49 c-0.154-0.086-0.203-0.186-0.186-0.354c0.059-0.57,0.117-1.143,0.162-1.713C42.716,30.702,42.42,29.77,41.598,29.166z">
                                                        </path>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>Pet Grooming</a>
                                </li>
                                <li>
                                    <a wire:navigate href="/pet-hotel"
                                        class="{{ $activeRoute === 'pet-hotel' ? 'text-primary-500' : 'text-gray-500 dark:text-white' }} flex items-center py-2 px-4 w-full hover:text-primary-600 dark:hover:text-primary-500">

                                        <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 50 50"
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#808080">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <path
                                                    d="M12.691406 0L11.564453 2.3320312L9 2.6386719L10.949219 4.3613281L10.435547 7L12.691406 5.6816406L14.949219 7L14.435547 4.3613281L16.384766 2.6386719L13.820312 2.3320312L12.691406 0 z M 14.949219 7L10.435547 7L9.3007812 7C6.3307812 7 4 9.3307812 4 12.300781L4 45C4 45.55 4.45 46 5 46L22 46L22 36L28 36L28 46L45 46C45.55 46 46 45.55 46 45L46 12.300781C46 9.3307812 43.669219 7 40.699219 7L39.564453 7L35.050781 7L31.359375 7L26.845703 7L23.154297 7L18.640625 7L14.949219 7 z M 18.640625 7L20.896484 5.6816406L23.154297 7L22.640625 4.3613281L24.589844 2.6386719L22.025391 2.3320312L20.896484 0L19.769531 2.3320312L17.205078 2.6386719L19.154297 4.3613281L18.640625 7 z M 26.845703 7L29.103516 5.6816406L31.359375 7L30.845703 4.3613281L32.794922 2.6386719L30.230469 2.3320312L29.103516 0L27.974609 2.3320312L25.410156 2.6386719L27.359375 4.3613281L26.845703 7 z M 35.050781 7L37.308594 5.6816406L39.564453 7L39.050781 4.3613281L41 2.6386719L38.435547 2.3320312L37.308594 0L36.179688 2.3320312L33.615234 2.6386719L35.564453 4.3613281L35.050781 7 z M 10 12L16 12L16 16L10 16L10 12 z M 22 12L28 12L28 16L22 16L22 12 z M 34 12L40 12L40 16L34 16L34 12 z M 10 20L16 20L16 24L10 24L10 20 z M 22 20L28 20L28 24L22 24L22 20 z M 34 20L40 20L40 24L34 24L34 20 z M 10 28L16 28L16 32L10 32L10 28 z M 22 28L28 28L28 32L22 32L22 28 z M 34 28L40 28L40 32L34 32L34 28 z M 10 36L16 36L16 40L10 40L10 36 z M 34 36L40 36L40 40L34 40L34 36 z">
                                                </path>
                                            </g>
                                        </svg>Pet Hotel</a>
                                </li>
                                <li>
                                    <a wire:navigate href="/breeding"
                                        class="{{ $activeRoute === 'breeding' ? 'text-primary-500' : 'text-gray-500 dark:text-white' }} flex items-center py-2 px-4 w-full hover:text-primary-600 dark:hover:text-primary-500">
                                        <svg class="mr-2 w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                            stroke="#808080" xml:space="preserve">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">

                                                <g>
                                                    <path class="st0"
                                                        d="M267.817,171.048c-12.343-12.388-27.371-22.176-44.078-28.53c-7.888,8.766-13.259,19.438-15.502,31.01 c-0.61,3.12-0.976,6.248-1.144,9.361c1.373,0.472,2.716,0.976,4.029,1.548c14.555,6.171,27.051,16.501,35.808,29.515 c7.033,10.405,11.702,22.451,13.274,35.504c0.412,3.257,0.61,6.576,0.61,9.963c0,11.306-2.273,21.956-6.377,31.674 c-5.096,11.985-12.984,22.588-22.886,30.835c-2.105,1.777-4.318,3.426-6.606,4.974c-13.014,8.788-28.546,13.884-45.497,13.884 c-11.275,0-21.909-2.281-31.643-6.378c-14.586-6.171-27.036-16.508-35.824-29.515c-8.758-12.992-13.854-28.561-13.854-45.474 c0-11.298,2.243-21.932,6.362-31.674c5.539-13.075,14.464-24.487,25.678-33.008c-0.214-2.953-0.306-5.905-0.306-8.857 c0-15.165,2.38-29.965,6.912-43.956c-17.348,6.309-32.91,16.34-45.665,29.126c-22.611,22.543-36.648,53.919-36.602,88.37 c-0.045,34.459,13.992,65.835,36.602,88.385c18.827,18.857,43.788,31.696,71.556,35.426v38.952h-57.901v33.55h57.901V512h33.566 v-56.268h57.947v-33.55H196.23v-38.952c27.784-3.73,52.775-16.569,71.587-35.426c6.286-6.286,11.886-13.228,16.752-20.742 c12.587-19.491,19.896-42.781,19.865-67.643c0-5.835-0.412-11.61-1.175-17.24C299.43,214.562,286.583,189.769,267.817,171.048z">
                                                    </path>
                                                    <path class="st0"
                                                        d="M349.015,0v33.551h51.203l-52.912,52.912c-22.276-16.981-49.052-25.564-75.691-25.54 c-31.903-0.024-64.019,12.22-88.37,36.617c-17.424,17.378-28.622,38.714-33.627,61.12c-0.061,0.274-0.091,0.549-0.168,0.778 c-0.198,0.87-0.366,1.747-0.533,2.579c-0.306,1.617-0.58,3.227-0.839,4.836c-0.168,0.969-0.305,1.984-0.412,2.952 c-0.198,1.473-0.366,2.983-0.488,4.493c-0.076,0.87-0.168,1.748-0.213,2.617c-0.03,0.435-0.061,0.877-0.091,1.343 c-0.077,0.877-0.107,1.777-0.138,2.646c-0.061,1.648-0.107,3.326-0.107,4.974c0,1.274,0.046,2.548,0.077,3.852 c0.061,1.282,0.138,2.548,0.198,3.861l0.198,2.822c0,0.466,0.076,0.938,0.106,1.442c2.686,27.874,14.723,55.124,36.037,76.4 c12.908,12.916,28.012,22.436,44.078,28.516c4.866-5.363,8.834-11.572,11.687-18.255c2.944-7.01,4.623-14.426,4.958-22.077 c-10.908-3.921-21.1-10.23-29.888-19.018c-7.949-7.987-13.899-17.118-17.851-26.914c-2.822-6.92-4.638-14.166-5.432-21.475 c-0.518-4.326-0.64-8.689-0.442-13.014c0.061-1.076,0.137-2.113,0.198-3.158c0.076-0.77,0.137-1.541,0.274-2.319 c0-0.298,0.061-0.595,0.092-0.9c0.076-0.74,0.168-1.51,0.305-2.243c0.168-1.007,0.336-2.052,0.534-3.051 c0.884-4.57,2.151-9.063,3.829-13.427c0.306-0.801,0.61-1.571,0.977-2.38c0.336-0.839,0.701-1.678,1.098-2.517 c0.336-0.839,0.733-1.679,1.175-2.487c0.366-0.831,0.808-1.64,1.282-2.441c2.212-4.104,4.822-8.055,7.812-11.817 c0.61-0.77,1.251-1.54,1.877-2.281c1.388-1.579,2.792-3.12,4.302-4.63c1.876-1.877,3.784-3.624,5.767-5.234 c6.5-5.401,13.624-9.635,21.1-12.648c14.662-5.942,30.804-7.453,46.199-4.463c15.41,2.99,29.995,10.36,41.988,22.344 c7.98,7.987,13.884,17.118,17.836,26.906c5.981,14.662,7.492,30.773,4.501,46.176c-3.021,15.394-10.406,29.988-22.337,41.973 c-2.624,2.616-5.37,5.035-8.223,7.178c0.167,2.952,0.259,5.905,0.259,8.818c0.046,15.036-2.35,29.858-6.942,43.956 c16.707-6.042,32.375-15.769,45.726-29.119c24.366-24.327,36.648-56.443,36.617-88.377c0.03-26.647-8.559-53.377-25.571-75.691 l52.912-52.912v51.226h33.55V0H349.015z">
                                                    </path>
                                                </g>
                                            </g>
                                        </svg>Breeding</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="shrink-0">
                        <a wire:navigate href="/trx-check" title=""
                            class="{{ $activeRoute === 'trx-check' ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Trx Check
                        </a>
                    </li>
                    <li class="shrink-0">
                        <a wire:navigate href="/contact-us" title=""
                            class="{{ $activeRoute === 'contact-us' ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} flex text-sm font-medium  hover:text-primary-700  dark:hover:text-primary-500">
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex items-center lg:space-x-2">

                <!-- Mobile Button -->
                <button wire:navigate href='/shopping-cart' type="button"
                    class="{{ str_starts_with($activeRoute, 'shopping-cart') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} sm:hidden flex items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white relative">
                    <!-- Badge Notification -->
                    <span
                        class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -right-1 dark:border-gray-900">
                        {{ $itemCount }}
                    </span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                    </svg>
                </button>

                <!-- Desktop Button -->
                <button
                    @if (request()->is('shopping-cart*')) wire:navigate href='/shopping-cart' @else
                    id="myCartDropdownButton1" data-dropdown-toggle="myCartDropdown1" @endif
                    type="button"
                    class="{{ str_starts_with($activeRoute, 'shopping-cart') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hidden sm:flex items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white relative">
                    <span class="sr-only">Cart</span>
                    <!-- Badge Notification -->
                    @if ($itemCount > 0)
                        <span
                            class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -right-1 dark:border-gray-900">
                            {{ $itemCount }}
                        </span>
                    @endif
                    <svg class="w-5 h-5 lg:me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                    </svg>
                    <span class="hidden sm:inline-flex items-center">
                        My Cart
                    </span>
                </button>

                @if (!request()->is('shopping-cart*'))
                    <div id="myCartDropdown1"
                        class="hidden w-100 z-10 mx-auto ml-2 rounded-lg bg-white p-5 shadow-sm border border-gray-200 dark:bg-gray-800 right-0 left-0"
                        wire:ignore.self>
                        <div class="space-y-2">
                            <!-- Product on cart -->
                            @if (count($cartItems) > 0)
                                @foreach ($cartItems as $item)
                                    <div class="flex items-center gap-4 p-1"
                                        wire:key="cart-item-{{ $item['id'] }}">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                            class="w-16 h-16 rounded-lg shadow-md">
                                        <div class="flex-1 min-w-0">
                                            <a href="#"
                                                class="block text-sm font-medium text-gray-900 dark:text-white hover:underline truncate max-w-[150px]">
                                                {{ $item['name'] }}
                                            </a>

                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                {{ format_currency($item['price']) }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                wire:click="decrementQuantity('{{ $item['id'] }}')"
                                                onclick="event.stopPropagation()"
                                                class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-red-400 rounded-lg hover:bg-red-500 focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 13H5v-2h14v2z" />
                                                </svg>
                                            </button>
                                            <input wire:model.lazy="cartItems.{{ $item['id'] }}.quantity"
                                                wire:change="updateItemQuantity('{{ $item['id'] }}', $event.target.value)"
                                                class="w-10 p-1 text-center text-sm font-medium border-gray-500 border rounded-lg dark:bg-gray-700 dark:text-white"
                                                value="{{ $item['quantity'] }}" value="1" min="1">
                                            <button type="button"
                                                wire:click="incrementQuantity('{{ $item['id'] }}')"
                                                onclick="event.stopPropagation()"
                                                class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-400 rounded-lg hover:bg-green-500 focus:ring-4 focus:ring-green-300 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6v-2z" />
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="removeItem('{{ $item['id'] }}')"
                                                class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                                <span class="sr-only">Remove</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- End product on cart -->
                                @endforeach
                            @else
                                <div class="items-center gap-4 p-1">
                                    <p class="text-lg text-gray-500 dark:text-gray-400 text-center py-4">
                                        Your cart is empty
                                    </p>
                                </div>
                            @endif
                            @if (count($cartItems) > 0)
                                <div class="flex justify-between text-base font-medium border-gray-400 border-t pt-1">
                                    <span class="text-gray-900 dark:text-white">Total:</span>
                                    <span class="text-gray-900 dark:text-white" id="cartTotal">
                                        {{ format_currency($this->total) }}</span>
                                </div>
                                <a wire:navigate href="/shopping-cart" wire:ignore
                                    class="mt-4 block w-full rounded-lg bg-primary-500 px-5 py-2 text-center text-sm font-medium text-white shadow-md hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                    View your cart
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

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
            <ul class="text-gray-900 dark:text-white text-sm font-medium space-y-3">
                <li>
                    <a wire:navigate href="/"
                        class="{{ request()->is('/') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Home</a>
                </li>
                <li>
                    <a wire:navigate href="/animals"
                        class="{{ request()->is('animals*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Animals</a>
                </li>
                <li>
                    <a wire:navigate href="/products"
                        class="{{ request()->is('products*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Products</a>
                </li>
                <li class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @mouseenter="if(window.innerWidth > 768) open = true"
                        @mouseleave="if(window.innerWidth > 768) open = false"
                        class="{{ request()->is('pet-grooming*', 'pet-hotel*', 'pet-clinic*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500 flex items-center justify-between w-full">
                        Service
                        <svg class="w-2.5 h-2.5 ml-2.5 transition-transform duration-200"
                            :class="{ 'rotate-180': open }" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-10 md:origin-top-left"
                        :class="{ 'relative mt-1 w-full': window.innerWidth <= 768 }" style="display: none;">
                        <li>
                            <a wire:navigate href="/pet-grooming" @click="open = false"
                                class="{{ request()->is('pet-grooming*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} block px-4 py-2 hover:text-primary-700 dark:hover:text-primary-500">Pet
                                Grooming</a>
                        </li>
                        <li>
                            <a wire:navigate href="/pet-hotel" @click="open = false"
                                class="{{ request()->is('pet-hotel*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} block px-4 py-2 hover:text-primary-700 dark:hover:text-primary-500">Pet
                                Hotel</a>
                        </li>
                        <li>
                            <a wire:navigate href="/breeding" @click="open = false"
                                class="{{ request()->is('breeding*') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} block px-4 py-2 hover:text-primary-700 dark:hover:text-primary-500">Pet
                                Clinic</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a wire:navigate href="/trx-check"
                        class="{{ request()->is('trx-check') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Trx
                        Check</a>
                </li>
                <li>
                    <a wire:navigate href="/contact-us"
                        class="{{ request()->is('contact-us') ? 'text-primary-500' : 'text-gray-900 dark:text-white' }} hover:text-primary-700 dark:hover:text-primary-500">Contact
                        Us</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('livewire:init', () => {
        // Pertahankan dropdown tetap terbuka setelah update
        Livewire.hook('commit', ({
            component,
            commit,
            respond,
            succeed,
            fail
        }) => {
            succeed(() => {
                if (component.name === 'navigation.main-navbar') {
                    const dropdown = document.getElementById('myCartDropdown1');
                    const button = document.getElementById('myCartDropdownButton1');
                }
            });
        });
    });
</script>
