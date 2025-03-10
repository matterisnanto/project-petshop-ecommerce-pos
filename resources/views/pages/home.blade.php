@extends('layouts.store')

@section('title', 'Home')

@section('content')
    {{-- store front --}}
    <section class="bg-primary-200 py-9 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto grid max-w-screen-xl px-4 pb-8 md:grid-cols-12 lg:gap-12 lg:pb-16 xl:gap-0">
            <!-- Gambar untuk layar mobile -->
            <div class="md:hidden mb-6">
                <img src="{{ url('/img/Catanddog-amico.png') }}" alt="shopping illustration" class="w-full" />
            </div>

            <!-- Teks dan tombol -->
            <div class="content-center justify-self-start md:col-span-7 md:text-start">
                <h1
                    class="mb-4 text-3xl font-extrabold leading-none tracking-tight dark:text-white md:max-w-2xl md:text-5xl xl:text-6xl">
                    Sayangi hewan peliharaan Anda, Manjakan Disisni!
                </h1>
                <p class="mb-4 max-w-2xl text-gray-500 dark:text-gray-400 md:mb-12 md:text-lg lg:mb-5 lg:text-xl">
                    Berikan makanan dan fasilitas yang baik
                </p>
                <a href="#"
                    class="inline-block rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    Shop Now
                </a>
            </div>

            <!-- Gambar untuk layar desktop -->
            <div class="hidden md:col-span-5 md:mt-0 md:flex">
                <img class="dark:hidden" src="{{ url('/img/Catanddog-amico.png') }}" alt="shopping illustration" />
                <img class="hidden dark:block" src="{{ url('/img/Catanddog-amico.png') }}" alt="shopping illustration" />
            </div>
        </div>
    </section>
    {{-- end store front --}}

    {{-- card with cta --}}
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 lg:py-8 px-4 mx-auto max-w-screen-xl">
            <h2
                class="mb-4 text-3xl font-extrabold tracking-tight leading-tight text-center text-gray-900 dark:text-white md:text-4xl">
                Brand
            </h2>
            <p class="mb-4 text-base font-light text-center text-gray-500 md:text-xl sm:px-14 md:px-24 lg:px-56 xl:px-72">
                Kami menjual berbagai macam makanan dan aksesoris peliharaan anda, dari berbagai macam brand ternama
            </p>

            <!-- Carousel for Mobile -->
            <div class="relative md:hidden mt-6" data-carousel="static">
                <!-- Carousel wrapper -->
                <div class="relative h-64 overflow-hidden rounded-lg"> <!-- Tinggi carousel disesuaikan -->
                    <!-- Brand Items -->
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/11/Royal-Canin-Logo.svg"
                                alt="Royal Canin" class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/Whiskas-Logo.svg"
                                alt="Whiskas" class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/Purina-Logo.svg"
                                alt="Purina" class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/pedigree-logo.svg"
                                alt="Pedigree" class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/Friskies-Logo.svg"
                                alt="Friskies" class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/meo.svg" alt="Me-O"
                                class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/hills-logo.svg" alt="Hills"
                                class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <a href="#"
                            class="flex justify-center items-center h-full p-4 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                            <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/bolt-cat.svg" alt="Bolt Cat"
                                class="w-48 h-32 object-contain"> <!-- Ukuran gambar diperbesar -->
                        </a>
                    </div>
                </div>
                <!-- Slider controls -->
                <button type="button"
                    class="absolute top-1/2 left-4 z-30 flex items-center justify-center -translate-y-1/2 w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-[#7c8c92]/30 hover:bg-[#7c8c92]/50 focus:ring-4 focus:ring-[#7c8c92]/70 focus:outline-none"
                    data-carousel-prev>
                    <svg aria-hidden="true" class="w-5 h-5 text-white sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="sr-only">Previous</span>
                </button>
                <button type="button"
                    class="absolute top-1/2 right-4 z-30 flex items-center justify-center -translate-y-1/2 w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-[#7c8c92]/30 hover:bg-[#7c8c92]/50 focus:ring-4 focus:ring-[#7c8c92]/70 focus:outline-none"
                    data-carousel-next>
                    <svg aria-hidden="true" class="w-5 h-5 text-white sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="sr-only">Next</span>
                </button>
            </div>

            <!-- Grid for Desktop -->
            <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-4 xl:gap-8 md:mb-8 md:mt-12">
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/11/Royal-Canin-Logo.svg"
                        alt="Royal Canin" class="h-20">
                </a>
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/Whiskas-Logo.svg" alt="Whiskas"
                        class="h-16">
                </a>
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/Purina-Logo.svg" alt="Purina"
                        class="h-16">
                </a>
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/pedigree-logo.svg" alt="Pedigree"
                        class="h-16">
                </a>
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/Friskies-Logo.svg" alt="Friskies"
                        class="h-16">
                </a>
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/meo.svg" alt="Me-O"
                        class="h-12">
                </a>
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/hills-logo.svg" alt="Hills"
                        class="h-12">
                </a>
                <a href="#"
                    class="flex justify-center items-center p-8 text-center bg-gray-50 rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800">
                    <img src="https://petshopindonesia.com/wp-content/uploads/2022/12/bolt-cat.svg" alt="Bolt Cat"
                        class="h-12">
                </a>
            </div>
        </div>
    </section>
    {{-- end card with cta --}}

    <!-- marketing start -->
    <section class="bg-primary-100 dark:bg-gray-900">
        <div
            class="gap-8 items-center py-4 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-2 xl:gap-16 sm:py-4 lg:px-6 ">
            <img class="mb-4 w-full lg:mb-0 rounded-lg"
                src="https://www.pethouse.co.id/cfind/source/images/retail/51%20jansen%20petshop.jpg" alt="feature image">
            <div class="text-gray-500 dark:text-gray-400 sm:text-lg">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Petshop
                </h2>
                <p class="mb-8 font-light lg:text-xl">Petshop kami adalah rumah bagi kucing dan anjing. Dengan produk
                    terbaik dan layanan lengkap, kami memastikan hewan peliharaan Anda selalu sehat, bahagia, dan
                    terawat.</p>
                <div class="py-8 mb-6 border-t border-b border-gray-200 dark:border-gray-700">
                    <div class="flex">
                        <div
                            class="flex justify-center items-center mr-4 w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 shrink-0">
                            {{-- <svg class="w-5 h-5 text-primary-600 dark:text-primary-300" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"></path>
                            </svg> --}}
                            <svg viewBox="-51.2 -51.2 614.40 614.40" id="Layer_1" version="1.1" xml:space="preserve"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                fill="#000000" transform="rotate(0)matrix(-1, 0, 0, 1, 0, 0)">
                                <g id="SVGRepo_bgCarrier" stroke-width="0">
                                    <rect x="-51.2" y="-51.2" width="614.40" height="614.40" rx="307.2"
                                        fill="#ffffff" strokewidth="0"></rect>
                                </g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <style type="text/css">
                                        .st0 {
                                            fill: #3f555d;
                                        }

                                        .st1 {
                                            fill: #3f555d;
                                        }
                                    </style>
                                    <g>
                                        <path class="st0"
                                            d="M462.1,173.6H176.9l-7.6-37.8l-6.4-32.1c-2.5-12.5-13.5-21.5-26.2-21.5h-78c-7.4,0-14.1,3-18.9,7.8 c-4.8,4.9-7.8,11.6-7.8,18.9c0,14.8,12,26.8,26.8,26.8h56l50.7,249.9c9.9-9.5,23.2-14.9,37.3-14.9c1.3,0,2.7,0.1,4,0.1l0.2,0 c0.5,0,0.9,0.1,1.4,0.1c0.5,0,1,0.1,1.5,0.2l0.5,0.1l0.7,0.1c0.3,0.1,0.7,0.1,1,0.2l0.3,0.1c0.4,0.1,0.8,0.1,1.1,0.2 c0.6,0.1,1.2,0.3,1.7,0.4l0.4,0.1c0.5,0.1,1.6,0.4,1.6,0.4l-4.6-21.2h228.5c8.9,0,16.5-6.5,17.8-15.3l21-142.1 C481.5,183.4,473.1,173.6,462.1,173.6z">
                                        </path>
                                        <path class="st0"
                                            d="M356.5,405.1h-82.2c-7.6,0-15.2-1-22.5-2.8c3.2,7,4.9,14.6,4.9,22.6c0,7.9-1.7,15.5-4.8,22.4 c6.8-1.7,13.8-2.5,20.9-2.5h83.6c7.6,0,15.2,1,22.5,2.8c-3.2-7-4.9-14.6-4.9-22.6c0-8,1.7-15.7,4.9-22.6 C371.7,404.1,364.2,405.1,356.5,405.1z">
                                        </path>
                                        <path class="st1"
                                            d="M223.5,390.6c-0.9-0.5-1.7-1-2.6-1.5c0,0,0,0,0,0c0,0-0.1,0-0.1,0v0c-1.9-1-4-1.8-6.1-2.5 c-0.4-0.1-0.8-0.2-1.2-0.3c-0.4-0.1-0.9-0.2-1.3-0.3c-0.5-0.1-1-0.2-1.5-0.3c-0.4-0.1-0.7-0.1-1.1-0.2c-0.4-0.1-0.8-0.1-1.2-0.2 c-0.5-0.1-1-0.1-1.5-0.2c-0.3,0-0.7-0.1-1.1-0.1c-1-0.1-2.1-0.1-3.1-0.1c-14.2,0-26.6,7.3-33.8,18.4c-4,6.2-6.3,13.7-6.3,21.7 c0,0.5,0,1,0,1.5c0,0.5,0.1,0.9,0.1,1.3c0,0.5,0.1,1.1,0.2,1.7c0,0.2,0,0.4,0.1,0.6c0.1,0.4,0.1,0.9,0.2,1.3l0,0.2 c0.1,0.6,0.2,1.1,0.3,1.7c0.1,0.6,0.3,1.2,0.4,1.8c0.3,1.2,0.7,2.4,1.1,3.6c0.4,1,0.8,2,1.2,3c0,0,0,0,0,0 c4.1,9.1,11.5,16.3,20.7,20.2c0.1,0,0.1,0.1,0.2,0.1c0.3,0.1,0.7,0.3,1,0.4c0.4,0.2,0.8,0.3,1.2,0.4c1.1,0.4,2.2,0.8,3.4,1.1 c0.7,0.2,1.3,0.3,1.9,0.4c0.6,0.1,1.1,0.2,1.6,0.3c0,0,0,0,0.1,0c0.5,0.1,1.1,0.2,1.6,0.2c0.8,0.1,1.5,0.2,2.3,0.2 c0.4,0,0.8,0,1.1,0c0.5,0,0.9,0,1.4,0c0.5,0,1,0,1.5,0c0.4,0,0.9,0,1.3-0.1c0.4,0,0.8-0.1,1.3-0.1c0.1,0,0.2,0,0.4,0 c0.4,0,0.8-0.1,1.2-0.1c0.5-0.1,0.9-0.1,1.4-0.2c1.4-0.2,2.7-0.5,4-0.9c0.4-0.1,0.9-0.3,1.3-0.4c0.5-0.1,1-0.3,1.5-0.5 c0.4-0.1,0.7-0.3,1.1-0.4c0.3-0.1,0.6-0.3,0.9-0.4c0.2-0.1,0.5-0.2,0.7-0.3c1.1-0.5,2.1-1,3.2-1.6c0.4-0.2,0.7-0.4,1.1-0.7 c0,0,0.1,0,0.1-0.1c11.5-7,19.2-19.7,19.2-34.2C242.8,410.4,235.1,397.7,223.5,390.6z M172.1,450.7c0.2,0.3,0.4,0.5,0.6,0.7 c0.4,0.5,0.9,0.9,1.3,1.4c-1.8-1.9-3.5-3.9-4.9-6.1c0.3,0.4,0.5,0.8,0.8,1.2C170.6,448.9,171.3,449.8,172.1,450.7z M169,446.6 c-0.6-0.9-1.1-1.8-1.6-2.7c0.2,0.3,0.4,0.7,0.6,1C168.3,445.5,168.6,446,169,446.6z M167.2,443.5c-0.1-0.1-0.1-0.3-0.2-0.4 C167,443.2,167.1,443.4,167.2,443.5z M174.4,453.3c0.4,0.4,0.8,0.8,1.3,1.2C175.2,454.1,174.8,453.7,174.4,453.3z M182.1,459.3 C182.1,459.3,182.1,459.3,182.1,459.3c-0.7-0.4-1.3-0.8-2-1.3C180.8,458.5,181.5,458.9,182.1,459.3z M179.7,457.7 c-0.6-0.4-1.2-0.9-1.8-1.3C178.5,456.9,179.1,457.3,179.7,457.7z M177.9,456.3c-0.2-0.1-0.3-0.3-0.5-0.4 C177.5,456.1,177.7,456.2,177.9,456.3z M176.6,455.3c-0.2-0.2-0.5-0.4-0.7-0.6C176.1,454.9,176.4,455.1,176.6,455.3z M184.7,460.7 c0.6,0.3,1.2,0.6,1.7,0.8C185.9,461.3,185.3,461,184.7,460.7z M202.7,441c-7.8,0-14.3-5.5-15.7-12.9c-0.2-1-0.3-2.1-0.3-3.1 c0-8.9,7.2-16,16-16c7.8,0,14.2,5.5,15.7,12.9c0.2,1,0.3,2.1,0.3,3.2C218.7,433.8,211.6,441,202.7,441z">
                                        </path>
                                        <path class="st1"
                                            d="M468.2,423.4c0-0.4,0-0.8-0.1-1.1c0-0.2,0-0.5-0.1-0.7c0-0.5-0.1-1-0.1-1.4c0,0,0-0.1,0-0.1 c0-0.2,0-0.4-0.1-0.5c-0.2-1.4-0.4-2.7-0.8-4c-0.1-0.3-0.2-0.6-0.3-1c-0.1-0.4-0.2-0.8-0.4-1.2c-0.1-0.4-0.3-0.8-0.4-1.2 c-0.1-0.4-0.3-0.8-0.4-1.2c-0.1-0.4-0.3-0.7-0.4-1.1c-0.2-0.5-0.4-0.9-0.6-1.4c-0.2-0.3-0.3-0.7-0.5-1c-0.2-0.4-0.4-0.8-0.6-1.1 c-0.1-0.3-0.3-0.5-0.4-0.8c-0.5-0.9-1-1.7-1.6-2.5c-0.2-0.4-0.5-0.7-0.7-1.1c0,0,0,0,0-0.1c0,0-0.1-0.1-0.1-0.1c0,0,0,0,0,0 c-0.2-0.3-0.4-0.6-0.7-0.9c0,0-0.1-0.1-0.1-0.1c-0.2-0.3-0.4-0.6-0.7-0.8c-0.2-0.3-0.5-0.6-0.7-0.9c0,0-0.1-0.1-0.1-0.1 c-0.4-0.4-0.8-0.9-1.2-1.3c-0.1-0.1-0.2-0.2-0.3-0.3c-0.1-0.1-0.2-0.1-0.2-0.2c-0.1-0.1-0.2-0.3-0.4-0.4c-0.3-0.3-0.6-0.6-0.9-0.8 c-0.2-0.2-0.4-0.4-0.6-0.6c-0.4-0.4-0.9-0.7-1.3-1.1c-0.4-0.4-0.9-0.7-1.3-1c-0.1-0.1-0.2-0.1-0.3-0.2c-0.2-0.2-0.4-0.3-0.6-0.4 c-1.3-0.9-2.7-1.8-4.1-2.6c-0.6-0.3-1.1-0.6-1.7-0.9c-1.5-0.7-3-1.3-4.5-1.8c-0.3-0.1-0.6-0.2-0.9-0.3c-0.4-0.1-0.8-0.3-1.3-0.4 c-0.4-0.1-0.9-0.2-1.3-0.3c-0.3-0.1-0.6-0.1-0.9-0.2c-0.2,0-0.4-0.1-0.6-0.1c-0.2,0-0.4-0.1-0.7-0.1c0,0-0.1,0-0.1,0 c-0.3,0-0.6-0.1-0.9-0.1c-0.5-0.1-0.9-0.1-1.4-0.2c-0.2,0-0.5-0.1-0.7-0.1c-0.1,0-0.3,0-0.4,0c-0.4,0-0.8-0.1-1.2-0.1 c-0.3,0-0.6,0-0.9,0c-0.5,0-1,0-1.5,0c-0.5,0-1,0-1.5,0c0,0,0,0,0,0c-0.3,0-0.6,0-0.9,0c-0.5,0-1,0.1-1.5,0.1 c-0.5,0-1.1,0.1-1.6,0.2c-0.3,0-0.6,0.1-0.9,0.1c-1.1,0.2-2.1,0.4-3.1,0.6c-0.4,0.1-0.7,0.2-1,0.3c-0.5,0.1-0.9,0.3-1.4,0.4 c-0.6,0.2-1.2,0.4-1.8,0.6c-1.3,0.5-2.6,1.1-3.9,1.7c-0.5,0.2-1,0.5-1.4,0.7c-0.6,0.3-1.1,0.6-1.7,1c-11.6,7-19.3,19.8-19.3,34.3 c0,14.5,7.7,27.3,19.3,34.3c0.3,0.2,0.6,0.3,0.9,0.5c0.2,0.1,0.5,0.3,0.7,0.4c0.3,0.2,0.6,0.3,0.9,0.5c0.4,0.2,0.8,0.4,1.2,0.6 c0.3,0.2,0.7,0.3,1,0.5c0.5,0.2,1,0.4,1.5,0.6c0.4,0.2,0.8,0.3,1.2,0.5c0,0,0,0,0.1,0c0.4,0.1,0.7,0.2,1.1,0.4 c0.5,0.1,0.9,0.3,1.4,0.4c0.4,0.1,0.8,0.2,1.2,0.3c0.3,0.1,0.7,0.2,1,0.2c0.3,0.1,0.7,0.1,1,0.2c0.4,0.1,0.7,0.1,1.1,0.2 c0.3,0,0.6,0.1,0.9,0.1c0.2,0,0.4,0.1,0.7,0.1c0.3,0,0.6,0.1,0.9,0.1c0.5,0.1,1,0.1,1.5,0.1c0.3,0,0.6,0,0.9,0c0,0,0,0,0,0 c0.5,0,1,0,1.5,0c0.5,0,1,0,1.5,0c0.3,0,0.6,0,0.9,0c0.4,0,0.8,0,1.2-0.1c0.1,0,0.3,0,0.4,0c0.2,0,0.5,0,0.7-0.1 c0.2,0,0.5-0.1,0.7-0.1c0.2,0,0.5-0.1,0.7-0.1c0.3,0,0.6-0.1,0.9-0.1c0,0,0.1,0,0.1,0c0.2,0,0.4-0.1,0.7-0.1c0.2,0,0.4-0.1,0.6-0.1 c0.5-0.1,1.1-0.2,1.6-0.4c0.4-0.1,0.8-0.2,1.2-0.3c0.5-0.1,1.1-0.3,1.6-0.5c0.4-0.1,0.8-0.3,1.3-0.5c1.8-0.7,3.6-1.5,5.2-2.4 c0.2-0.1,0.4-0.2,0.6-0.3c0.8-0.5,1.7-1,2.5-1.6c0.3-0.2,0.6-0.4,0.9-0.6c0.2-0.1,0.4-0.3,0.6-0.4c0.2-0.1,0.4-0.3,0.6-0.4 c0.4-0.3,0.9-0.7,1.3-1c0.3-0.3,0.6-0.5,0.9-0.8c0.2-0.2,0.4-0.4,0.6-0.6c0.2-0.2,0.5-0.4,0.7-0.7c0.1-0.1,0.2-0.2,0.4-0.4 c0.2-0.2,0.4-0.5,0.7-0.7c0.2-0.2,0.4-0.5,0.7-0.7c0.2-0.2,0.4-0.5,0.6-0.7c0.2-0.2,0.3-0.4,0.5-0.6c0.3-0.4,0.7-0.8,1-1.3 c0.2-0.3,0.4-0.6,0.7-0.9c0.1-0.1,0.1-0.2,0.2-0.3c0.2-0.3,0.4-0.5,0.5-0.8c0.3-0.5,0.6-0.9,0.9-1.4c0.1-0.2,0.3-0.4,0.4-0.7 c0.1-0.1,0.1-0.2,0.2-0.3c0.2-0.3,0.3-0.6,0.5-0.8c0.2-0.3,0.3-0.6,0.4-0.9c0.7-1.4,1.4-2.9,2-4.5c0.1-0.3,0.2-0.6,0.3-0.9 c0.1-0.2,0.2-0.5,0.2-0.7c0.1-0.3,0.2-0.6,0.3-0.8c0.1-0.3,0.2-0.6,0.2-0.8c0.1-0.2,0.1-0.4,0.2-0.6c0.1-0.3,0.1-0.5,0.2-0.8 c0.1-0.3,0.2-0.6,0.2-1c0.1-0.5,0.2-0.9,0.3-1.4c0.1-0.4,0.1-0.8,0.2-1.1c0-0.3,0.1-0.5,0.1-0.8c0.1-0.5,0.1-0.9,0.1-1.4 c0.1-0.7,0.1-1.4,0.1-2.1c0-0.2,0-0.4,0-0.6c0-0.2,0-0.5,0-0.7c0-0.4,0-0.8,0-1.3C468.2,423.6,468.2,423.5,468.2,423.4z M428.2,441 c-8.9,0-16-7.2-16-16c0-8.9,7.2-16,16-16c8.9,0,16,7.2,16,16C444.2,433.8,437,441,428.2,441z">
                                        </path>
                                        <rect class="st1" height="112.7" width="135" x="255.9" y="47"></rect>
                                        <rect class="st0" height="77.3" width="36.8" x="205.1" y="82.3"></rect>
                                        <rect class="st0" height="77.3" width="36.8" x="404.9" y="82.3"></rect>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Produk Lengkap dan
                                Berkualitas Tinggi</h3>
                            <p class="mb-2 font-light text-gray-500 dark:text-gray-400">Kami menyediakan berbagai
                                produk kebutuhan hewan peliharaan, mulai dari makanan premium, perlengkapan, hingga
                                aksesoris. Semua produk kami dipilih dengan cermat untuk memastikan kualitas terbaik dan
                                aman untuk hewan peliharaan Anda.</p>
                        </div>
                    </div>
                    <div class="flex pt-8">
                        <div
                            class="flex justify-center items-center mr-4 w-8 h-8 bg-purple-100 rounded-full dark:bg-purple-900 shrink-0">
                            <svg viewBox="-51.2 -51.2 614.40 614.40" id="Layer_1" version="1.1" xml:space="preserve"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                fill="#000000">
                                <g id="SVGRepo_bgCarrier" stroke-width="0">
                                    <rect x="-51.2" y="-51.2" width="614.40" height="614.40" rx="307.2"
                                        fill="#ffffff" strokewidth="0"></rect>
                                </g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <style type="text/css">
                                        .st0 {
                                            fill: #3f555d;
                                        }

                                        .st1 {
                                            fill: #f4abd1;
                                        }
                                    </style>
                                    <polygon class="st0"
                                        points="326.5,145.6 326.5,195.4 293.4,195.4 293.4,228.5 243.6,228.5 243.6,195.4 210.5,195.4 210.5,145.6 243.6,145.6 243.6,112.5 293.4,112.5 293.4,145.6 ">
                                    </polygon>
                                    <polygon class="st1"
                                        points="326.5,145.6 326.5,195.4 293.4,195.4 293.4,228.5 243.6,228.5 243.6,195.4 210.5,195.4 210.5,145.6 243.6,145.6 243.6,112.5 293.4,112.5 293.4,145.6 ">
                                    </polygon>
                                    <path class="st0"
                                        d="M393,244.1c12.8-21.6,20.1-46.7,20.1-73.6c0-79.9-64.8-144.6-144.6-144.6c-79.9,0-144.6,64.8-144.6,144.6 c0,0.3,0,0.7,0,1h0V199l-23.6,33.8c-3.4,4.8-0.5,11.5,5.2,12.4l18.3,3l10.1,88.4c1,8.8,7.3,16,15.9,18.3c15.4,4.1,43,9.5,81,9.5 c0,0,5.4,96.1-20.2,121.7H258v-59.8c0-3.9,3.1-7,7-7s7,3.1,7,7v59.8h119.2c0,0-11.7-32.8-21.5-66.6c-10.2-35.2-11.4-72.4-3.5-108.3 C371.3,287.5,380.6,265,393,244.1z M271.9,402.5c-0.1,0.4-0.2,0.9-0.4,1.3c-0.2,0.4-0.4,0.8-0.6,1.2c-0.3,0.4-0.5,0.7-0.9,1.1 c-1.3,1.3-3.1,2-5,2c-0.5,0-0.9,0-1.4-0.1c-0.4-0.1-0.9-0.2-1.3-0.4c-0.4-0.2-0.8-0.4-1.2-0.6c-0.4-0.3-0.7-0.6-1.1-0.9 c-0.3-0.3-0.6-0.7-0.9-1.1c-0.3-0.4-0.5-0.8-0.6-1.2c-0.2-0.4-0.3-0.9-0.4-1.3c-0.1-0.4-0.1-0.9-0.1-1.4c0-0.5,0-0.9,0.1-1.4 c0.1-0.4,0.2-0.9,0.4-1.3c0.2-0.4,0.4-0.8,0.6-1.2c0.3-0.4,0.5-0.7,0.9-1.1c0.3-0.3,0.7-0.6,1.1-0.9c0.4-0.3,0.8-0.5,1.2-0.7 c0.4-0.2,0.9-0.3,1.3-0.4c2.3-0.4,4.7,0.3,6.3,1.9c1.3,1.3,2,3.1,2,4.9C272,401.6,271.9,402.1,271.9,402.5z M333.5,247 c-17.5,14.9-40.2,23.9-65,23.9c-55.1,0-99.9-44.4-100.4-99.4c0-0.3,0-0.7,0-1C168,115,213,70,268.5,70S369,115,369,170.5 C369,201.1,355.2,228.6,333.5,247z">
                                    </path>
                                </g>
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Layanan Profesional dan
                                Terpercaya</h3>
                            <p class="mb-2 font-light text-gray-500 dark:text-gray-400">Petshop kami didukung oleh tim
                                ahli yang berpengalaman di bidangnya, mulai dari groomer profesional hingga dokter hewan
                                bersertifikat. Layanan grooming kami tidak hanya membuat hewan peliharaan Anda tampil
                                menawan, tetapi juga menjaga kesehatan kulit dan bulu mereka</p>

                        </div>
                    </div>
                    <div class="flex pt-8">
                        <div
                            class="flex justify-center items-center mr-4 w-8 h-8 bg-teal-100 rounded-full dark:bg-teal-900 shrink-0">
                            <svg viewBox="-51.2 -51.2 614.40 614.40" id="Layer_1" version="1.1" xml:space="preserve"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                fill="#000000">
                                <g id="SVGRepo_bgCarrier" stroke-width="0">
                                    <rect x="-51.2" y="-51.2" width="614.40" height="614.40" rx="307.2"
                                        fill="#ffffff" strokewidth="0"></rect>
                                </g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <style type="text/css">
                                        .st0 {
                                            fill: #3f555d;
                                        }

                                        .st1 {
                                            fill: #f4abd1;
                                        }
                                    </style>
                                    <g>
                                        <g>
                                            <path class="st0"
                                                d="M201.7,333c-8.4-7.3-15.9-15.6-22.4-24.7c-0.6-0.8-1.2-1.6-1.7-2.5c-8.1,2.8-16.8,4.4-25.8,4.8 c-1.2,0-2.5,0.1-3.7,0.1s-2.5,0-3.7-0.1c-13.7-0.5-26.7-4.1-38.2-10c0.6-1,0.9-2.2,0.9-3.4v-44.3c0-3.9-3.1-7-7-7s-7,3.1-7,7v39.5 c-22.3-16.7-36.8-43.4-36.8-73.5c0-50.7,41.1-91.8,91.8-91.8c13.9,0,27,3.1,38.8,8.6c3.2,1.5,6.4,3.2,9.4,5.1 c1.2,0.7,2.4,1.5,3.5,2.3c0,0,0,0,0.1,0.1c1.8,1.2,3.5,2.5,5.2,3.8c1,0.8,1.9,1.5,2.8,2.3c1.8,1.5,3.5,3.1,5.1,4.8 c1,1,2,2.1,3,3.1c0.5,0.5,1,1.1,1.4,1.6c0.9,1.1,1.9,2.2,2.7,3.3c0,0,0,0,0,0c0.8,1,1.5,2,2.3,3c0.5,0.7,1.1,1.5,1.6,2.2 c0,0,0,0,0,0c0.6,0.8,1.1,1.7,1.7,2.6c0.5,0.9,1.1,1.7,1.5,2.5c0.4,0.7,0.8,1.3,1.1,2l0,0c1,1.8,1.9,3.6,2.8,5.5 c5.6,11.8,8.6,25,8.6,38.9c0,17.5-4.9,33.9-13.4,47.8c0.9,1.5,1.8,2.9,2.7,4.3c5.5,8.1,12.5,15.1,20.6,20.6 c0.3,0.2,0.6,0.4,0.9,0.6c14.8-20.6,23.5-45.9,23.5-73.3c0-25.5-7.5-49.2-20.6-69l-1.2-1.7c-0.3-0.4-0.5-0.8-0.8-1.1 c-0.3-0.4-0.5-0.7-0.8-1.1c-0.7-1-1.5-2-2.2-3c-0.3-0.4-0.6-0.8-0.9-1.1c-3-3.9-6.3-7.6-9.8-11.2c-0.5-0.5-0.9-1-1.4-1.4 c-1.1-1.1-2.3-2.2-3.4-3.2c-0.5-0.4-0.9-0.9-1.4-1.3c-0.6-0.5-1.1-1-1.7-1.5c-1.1-1-2.3-1.9-3.4-2.8 c-21.6-17.2-48.9-27.5-78.6-27.5C78.4,92.8,22,149.3,22,218.9c0,64.4,48.2,117.5,110.5,125.1v53.4H96.3v31h36.3v52.8h31v-52.8 h36.3v-31h-36.3V344C177.1,342.3,189.9,338.6,201.7,333z">
                                            </path>
                                            <g>
                                                <path class="st1"
                                                    d="M403.7,30.8v31H437l-66,66c-21.4-16.7-48.3-26.6-77.5-26.6c-19.2,0-37.3,4.3-53.6,11.9 c8.4,7.3,15.9,15.6,22.4,24.7c0.6,0.8,1.1,1.6,1.7,2.5c9.3-3.2,19.2-4.9,29.5-4.9c24.1,0,46,9.3,62.3,24.4 c1.8,1.6,3.5,3.3,5.1,5.1c4.1,4.4,7.8,9.3,10.9,14.5h-33.2c-3.9,0-7,3.1-7,7s3.1,7,7,7h37.1c1,0,2-0.2,2.9-0.6 c4.3,10.7,6.7,22.3,6.7,34.5c0,50.7-41.1,91.8-91.8,91.8c-14.1,0-27.4-3.2-39.3-8.8c-4.3-2.1-8.5-4.4-12.4-7.1 c-9.5-6.5-17.8-14.7-24.3-24.3c-2.8-4.1-5.3-8.5-7.4-13.1c-5.4-11.7-8.4-24.8-8.4-38.5c0-17.5,4.9-33.9,13.4-47.8 c-0.8-1.4-1.8-2.9-2.7-4.3c-5.5-8.1-12.4-15.1-20.6-20.6c-0.3-0.2-0.6-0.4-0.9-0.6c-14.8,20.7-23.5,46-23.5,73.3 c0,22.2,5.7,43,15.8,61.1c2.3,4.1,4.8,8.1,7.5,11.9c6.8,9.6,15,18.2,24.2,25.6c4,3.2,8.1,6.1,12.4,8.7 c19.3,11.9,41.9,18.8,66.2,18.8c69.6,0,126.1-56.4,126.1-126.1c0-29.2-9.9-56.1-26.6-77.5l66-66v34.5h31V30.8H403.7z">
                                                </path>
                                            </g>
                                            <path class="st0"
                                                d="M95.1,222.9c-1.3,1.3-2,3.1-2,4.9c0,1.8,0.8,3.7,2,5c1.3,1.3,3.1,2.1,5,2.1c0.5,0,0.9,0,1.4-0.1 c0.4-0.1,0.9-0.2,1.3-0.4c0.4-0.2,0.8-0.4,1.2-0.6c0.4-0.3,0.7-0.5,1.1-0.9c1.3-1.3,2-3.1,2-5c0-1.8-0.8-3.6-2-4.9 c-1.3-1.3-3.1-2.1-5-2.1S96.4,221.6,95.1,222.9z">
                                            </path>
                                            <g>
                                                <path class="st1"
                                                    d="M314.5,190.3c0.3,0.4,0.5,0.7,0.9,1.1c1.3,1.3,3.1,2,5,2c0.5,0,0.9,0,1.4-0.1c0.4-0.1,0.9-0.2,1.3-0.4 c0.4-0.2,0.8-0.4,1.2-0.6c0.4-0.3,0.8-0.6,1.1-0.9c0.3-0.3,0.6-0.7,0.9-1.1c0.3-0.4,0.5-0.8,0.6-1.2c0.2-0.4,0.3-0.9,0.4-1.3 c0.1-0.5,0.1-0.9,0.1-1.4s0-0.9-0.1-1.4c-0.1-0.4-0.2-0.9-0.4-1.3c-0.2-0.4-0.4-0.8-0.6-1.2c-0.3-0.4-0.5-0.8-0.9-1.1 c-1.3-1.3-3.1-2-5-2s-3.6,0.8-5,2c-0.3,0.3-0.6,0.7-0.9,1.1c-0.3,0.4-0.5,0.8-0.6,1.2c-0.2,0.4-0.3,0.9-0.4,1.3 c-0.1,0.5-0.1,0.9-0.1,1.4s0,0.9,0.1,1.4c0.1,0.4,0.2,0.9,0.4,1.3C314,189.5,314.3,189.9,314.5,190.3z">
                                                </path>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Layanan Pacak/Kawin yang
                                Aman dan Terjamin</h3>
                            <p class="mb-2 font-light text-gray-500 dark:text-gray-400">Kami menyediakan layanan
                                pacak/kawin untuk kucing dan anjing ras dengan proses yang aman, terpercaya, dan
                                dikelola oleh ahli.</p>
                        </div>
                    </div>
                </div>
                <p class="text-sm">Kami peduli dengan setiap hewan peliharaan seperti keluarga sendiri. Mari
                    bersama-sama memberikan yang terbaik untuk mereka!</p>
            </div>
        </div>
    </section>
    <!-- marketing end -->

    <!-- card image start -->
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="text-center text-gray-900">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 lg:text-5xl dark:text-white">
                    Penuhi Segala Kebutuhan Hewan Peliharaan Anda dengan Solusi Terlengkap dan Terbaik!</h2>
            </div>
            <div class="grid gap-6 mt-12 lg:mt-14 lg:gap-12 md:grid-cols-3">
                <div class="flex mb-2 md:flex-col md:mb-0">
                    <img class="mr-4 w-auto h-36 md:w-full md:h-auto rounded-lg"
                        src="https://img.freepik.com/free-photo/pet-accessories-with-full-food-bowl_23-2148949593.jpg"
                        alt="office image" />
                    <div>
                        <h3 class="text-xl font-bold md:mt-4 mb-2.5 text-gray-900 dark:text-white">Pet Food
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">Nutrisi Terbaik untuk Sahabat Setia Anda – Karena
                            Mereka Layak Mendapatkan yang Terbaik!</p>
                    </div>
                </div>
                <div class="flex mb-2 md:flex-col md:mb-0">
                    <img class="mr-4 w-auto h-36 md:w-full md:h-auto rounded-lg"
                        src="https://img.freepik.com/premium-photo/vertical-portrait-female-groomer-washing-adorable-curly-labradoodle-dog-with-shampoo-bathtub-grooming-salon-prepare-cut-unrecognizable-woman-owner-carefully-washes-pet-fur-home_482921-6271.jpg"
                        alt="office image 2" />
                    <div>
                        <h3 class="text-xl font-bold md:mt-4 mb-2.5 text-gray-900 dark:text-white">Pet Grooming</h3>
                        <p class="text-gray-500 dark:text-gray-400">Bulu Bersih, Hati Senang – Grooming Terbaik untuk
                            Teman Setia Anda.</p>
                    </div>
                </div>
                <div class="flex md:flex-col">
                    <img class="mr-4 w-auto h-36 md:w-full md:h-auto rounded-lg"
                        src="https://i.pinimg.com/originals/d8/00/17/d80017ad22c34882a52a6110975703ee.jpg"
                        alt="office image 3" />
                    <div>
                        <h3 class="text-xl font-bold md:mt-4 mb-2.5 text-gray-900 dark:text-white">Pet Clinic
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">Karena Mereka Bagian dari Keluarga – Berikan yang
                            Terbaik untuk Kesehatan Peliharaan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- card image end -->


    <!-- carousel start -->
    <section class="bg-primary-100 dark:bg-gray-900 lg:py-16 py-8">
        <div class="px-4 mx-auto mb-8 max-w-screen-md text-center md:mb-16 lg:px-0">
            <h2 class="mb-4 text-3xl tracking-tight font-extrabold text-gray-900 md:text-4xl dark:text-white">
                Setiap Pelanggan adalah Keluarga, Setiap Hewan adalah Sahabat.</h2>
        </div>
        <div class="mx-auto max-w-screen-xl">
            <div id="animation-carousel" class="relative px-16 sm:px-24" data-carousel="slide">
                <div class="overflow-hidden relative h-48 rounded-lg sm:h-64 xl:h-80 2xl:h-80">
                    <div class="grid hidden absolute inset-0 gap-8 transition-all duration-700 ease-linear transform lg:grid-cols-2"
                        data-carousel-item="">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/office-work.png"
                            class="block w-full h-full rounded-lg" alt="...">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/office.png"
                            class="hidden w-full h-full rounded-lg lg:block" alt="...">
                    </div>
                    <div class="grid hidden absolute inset-0 gap-8 transition-all duration-700 ease-linear transform lg:grid-cols-2"
                        data-carousel-item="">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/conference.png"
                            class="block w-full h-full rounded-lg" alt="...">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/girl-with-phone.png"
                            class="hidden w-full h-full rounded-lg lg:block" alt="...">
                    </div>
                    <div class="grid hidden absolute inset-0 gap-8 transition-all duration-700 ease-linear transform lg:grid-cols-2"
                        data-carousel-item="">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/man-at-office.png"
                            class="block w-full h-full rounded-lg" alt="...">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/meeting.png"
                            class="hidden w-full h-full rounded-lg lg:block" alt="...">
                    </div>
                    <div class="grid hidden absolute inset-0 gap-8 transition-all duration-700 ease-linear transform lg:grid-cols-2"
                        data-carousel-item="">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/work-from-home.png"
                            class="block w-full h-full rounded-lg" alt="...">
                        <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/social-proof/carousel-slider/workspace.png"
                            class="hidden w-full h-full rounded-lg lg:block" alt="...">
                    </div>
                </div>
                <button type="button"
                    class="flex absolute top-0 left-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none"
                    data-carousel-prev="">
                    <span
                        class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 dark:bg-white/30 bg-gray-800/30 dark:group-hover:bg-white/50 group-hover:bg-gray-800/60 group-focus:ring-4 dark:group-focus:ring-white group-focus:ring-gray-800/70 group-focus:outline-none">
                        <svg class="w-5 h-5 text-white sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        <span class="hidden">Previous</span>
                    </span>
                </button>
                <button type="button"
                    class="flex absolute top-0 right-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none"
                    data-carousel-next="">
                    <span
                        class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 dark:bg-white/30 bg-gray-800/30 dark:group-hover:bg-white/50 group-hover:bg-gray-800/60 group-focus:ring-4 dark:group-focus:ring-white group-focus:ring-gray-800/70 group-focus:outline-none">
                        <svg class="w-5 h-5 text-white sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        <span class="hidden">Next</span>
                    </span>
                </button>
            </div>
        </div>
        <dl
            class="grid grid-cols-2 gap-8 mx-auto mt-8 max-w-screen-xl text-gray-900 lg:mt-14 sm:grid-cols-3 xl:grid-cols-6 dark:text-white">
            <div class="flex flex-col justify-center items-center">
                <dt class="mb-2 text-3xl font-extrabold md:text-4xl">73M+</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Developers</dd>
            </div>
            <div class="flex flex-col justify-center items-center">
                <dt class="mb-2 text-3xl font-extrabold md:text-4xl">100M+</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Public repositories</dd>
            </div>
            <div class="flex flex-col justify-center items-center">
                <dt class="mb-2 text-3xl font-extrabold md:text-4xl">1000s</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Open source projects</dd>
            </div>
            <div class="flex flex-col justify-center items-center">
                <dt class="mb-2 text-3xl font-extrabold md:text-4xl">1B+</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Contributors</dd>
            </div>
            <div class="flex flex-col justify-center items-center">
                <dt class="mb-2 text-3xl font-extrabold md:text-4xl">90+</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Top Forbes companies</dd>
            </div>
            <div class="flex flex-col justify-center items-center">
                <dt class="mb-2 text-3xl font-extrabold md:text-4xl">4M+</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Organizations</dd>
            </div>
        </dl>
    </section>
    <!-- carousel  end -->

    <!-- testimonial start -->
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 lg:px-6">
            <div class="mx-auto max-w-screen-sm mb-8 lg:mb-16">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Testimoni</h2>
                <p class="font-light text-gray-500 sm:text-xl dark:text-gray-400">Explore the whole collection of
                    open-source web components and elements built with the utility classes from Tailwind</p>
            </div>
            <div id="testimonial-carousel" class="relative" data-carousel="slide">
                <div class="overflow-x-hidden overflow-y-visible relative mx-auto max-w-screen-md h-52 rounded-lg sm:h-48">
                    <figure class="hidden mx-auto w-full max-w-screen-md" data-carousel-item>
                        <blockquote>
                            <p class="text-lg font-medium text-gray-900 sm:text-2xl dark:text-white">"Flowbite is just
                                awesome. It contains tons of predesigned components and pages starting from login screen
                                to complex dashboard. Perfect choice for your next SaaS application."</p>
                        </blockquote>
                        <figcaption class="flex justify-center items-center mt-6 space-x-3">
                            <img class="w-6 h-6 rounded-full"
                                src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
                                alt="profile picture">
                            <div class="flex items-center divide-x-2 divide-gray-500 dark:divide-gray-700">
                                <div class="pr-3 font-medium text-gray-900 dark:text-white">Bonnie Green</div>
                                <div class="pl-3 text-sm font-light text-gray-500 dark:text-gray-400">Web developer at
                                    Google</div>
                            </div>
                        </figcaption>
                    </figure>
                    <figure class="hidden mx-auto w-full max-w-screen-md" data-carousel-item>
                        <blockquote>
                            <p class="text-lg font-medium text-gray-900 sm:text-2xl dark:text-white">"As someone who
                                mainly designs in the browser, I've been a casual user of Figma, but as soon as I saw
                                and started playing with FlowBite my mind was blown and became so productive."</p>
                        </blockquote>
                        <figcaption class="flex justify-center items-center mt-6 space-x-3">
                            <img class="w-6 h-6 rounded-full"
                                src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/helene-engels.png"
                                alt="profile picture">
                            <div class="flex items-center divide-x-2 divide-gray-500 dark:divide-gray-700">
                                <div class="pr-3 font-medium text-gray-900 dark:text-white">Helene Engels</div>
                                <div class="pl-3 text-sm font-light text-gray-500 dark:text-gray-400">Creative designer
                                    at Adobe</div>
                            </div>
                        </figcaption>
                    </figure>
                    <figure class="hidden mx-auto w-full max-w-screen-md" data-carousel-item>
                        <blockquote>
                            <p class="text-lg font-medium text-gray-900 sm:text-2xl dark:text-white">"Flowbite has code
                                in one place and I'm not joking when I say it took me a matter of minutes to copy the
                                code, customise it and integrate within a Laravel + Vue application."</p>
                        </blockquote>
                        <figcaption class="flex justify-center items-center mt-6 space-x-3">
                            <img class="w-6 h-6 rounded-full"
                                src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/neil-sims.png"
                                alt="profile picture">
                            <div class="flex items-center divide-x-2 divide-gray-500 dark:divide-gray-700">
                                <div class="pr-3 font-medium text-gray-900 dark:text-white">Neil Sims</div>
                                <div class="pl-3 text-sm font-light text-gray-500 dark:text-gray-400">CTO at Microsoft
                                </div>
                            </div>
                        </figcaption>
                    </figure>
                </div>
                <div class="flex justify-center items-center">
                    <button type="button"
                        class="flex justify-center items-center mr-4 h-full cursor-pointer group focus:outline-none"
                        data-carousel-prev>
                        <span class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="hidden">Previous</span>
                        </span>
                    </button>
                    <button type="button"
                        class="flex justify-center items-center h-full cursor-pointer group focus:outline-none"
                        data-carousel-next>
                        <span class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="hidden">Next</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonial end -->

@endsection
