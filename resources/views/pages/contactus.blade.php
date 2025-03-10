@extends('layouts.store')

@section('title', 'Contact Us')

@section('content')
    <!-- Block start -->
    {{-- <p>Current URL: {{ request()->url() }}</p>
    <p>Current URL: {{ request()->path() }}</p>
    <p>Is Home: {{ request()->is('/') ? 'Yes' : 'No' }}</p>
    <p>Is Product: {{ request()->is('product*') ? 'Yes' : 'No' }}</p>
    <p>Is Trx: {{ request()->is('trx*') ? 'Yes' : 'No' }}</p>
    <p>Is Contact Us: {{ request()->is('contact-us') ? 'Yes' : 'No' }}</p> --}}
    <div class="bg-primary-100 dark:bg-gray-800 w-full py-8">
        <div class="px-4 mx-auto max-w-screen-sm text-center lg:px-6">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Contact Us</h2>
            <p class="font-light text-gray-600 dark:text-gray-400 sm:text-xl">Kami berkomitmen untuk merespons dengan cepat
                dan memberikan solusi yang sesuai dengan kebutuhan Anda.</p>
        </div>
    </div>
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <div class="grid grid-cols-1 lg:gap-8 lg:grid-cols-3">
                <div class="col-span-2 mb-8 lg:mb-0">
                    <form action="#" class="grid grid-cols-1 gap-8 mx-auto max-w-screen-md sm:grid-cols-2">
                        <div>
                            <label for="first-name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nama Depan</label>
                            <input type="text" id="first-name"
                                class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light"
                                placeholder="James" required>
                        </div>
                        <div>
                            <label for="last-name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nama
                                Belakang</label>
                            <input type="text" id="last-name"
                                class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light"
                                placeholder="Bowen" required>
                        </div>
                        <div>
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Email</label>
                            <input type="email" id="email"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light"
                                placeholder="jamesbowen@mail.com" required>
                        </div>
                        <div>
                            <label for="phone-number"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nomor
                                Telepon</label>
                            <input type="number" id="phone-number"
                                class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light"
                                placeholder="+17 852 881" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="message"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Pesan</label>
                            <textarea id="message" rows="6"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Tinggalkan Pesan..."></textarea>
                            <p class="mt-4 text-sm text-gray-500">Dengan mengirimkan formulir ini, Anda menyetujui <a
                                    href="#" class="text-primary-600 hover:underline dark:text-primary-500">syarat
                                    dan ketentuan </a>
                                serta <a href="#"
                                    class="text-primary-600 hover:underline dark:text-primary-500">kebijakan privasi</a>
                                kami
                                yang menjelaskan bagaimana kami dapat
                                mengumpulkan, menggunakan, dan mengungkapkan informasi pribadi Anda termasuk kepada pihak
                                ketiga.</p>
                        </div>
                        <button type="submit"
                            class="py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 sm:w-fit hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Kirim
                            Pesan</button>
                    </form>
                </div>
                <div class="grid grid-cols-1 col-span-1 gap-8 text-center sm:grid-cols-2 lg:grid-cols-1">
                    <div>
                        <div
                            class="flex justify-center items-center mx-auto mb-4 w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800 lg:h-16 lg:w-16">
                            <svg class="w-5 h-5 text-gray-600 lg:w-8 lg:h-8 dark:text-gray-500" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <p class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Informasi perusahaans:</p>
                        <p class="text-gray-500 dark:text-gray-400">Petshop <br>Tax id: USXXXXXX</p>
                    </div>
                    <div>
                        <div
                            class="flex justify-center items-center mx-auto mb-4 w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800 lg:h-16 lg:w-16">
                            <svg class="w-5 h-5 text-gray-600 lg:w-8 lg:h-8 dark:text-gray-500" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <p class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Alamat :</p>
                        <p class="text-gray-500 dark:text-gray-400">Kabupaten Bogor <br> Zip
                            Code/Postal code:03875</p>
                    </div>
                    <div>
                        <div
                            class="flex justify-center items-center mx-auto mb-4 w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800 lg:h-16 lg:w-16">
                            <svg class="w-5 h-5 text-gray-600 lg:w-8 lg:h-8 dark:text-gray-500" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                </path>
                            </svg>
                        </div>
                        <p class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Hubungi Kami :</p>
                        <p class="mb-3 text-gray-500 dark:text-gray-400">Hubungi kami untuk berbicara dengan anggota tim
                            kami. Kami selalu senang membantu.</p>
                        <p class="font-semibold text-primary-600 dark:text-primary-500">+62456484675346</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Block end -->
@endsection
