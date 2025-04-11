<div>
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6 ">
            <div class="mx-auto max-w-screen-md text-center">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Periksa Trx Id
                    Disini
                </h2>
                <p class="mb-6 font-light text-gray-500 md:text-lg dark:text-gray-400">Trx ID (Transaction ID) adalah
                    kode
                    unik yang diberikan setelah sebuah transaksi berhasil dilakukan. Kode ini berfungsi sebagai
                    identifikasi
                    transaksi yang dapat digunakan untuk melacak, memverifikasi, atau mengecek status transaksi
                    tersebut.
                </p>
                <form action="/transaction/search" method="POST" class="mx-auto max-w-screen-sm">
                    <div class="flex items-center mb-3">
                        <div class="relative mr-3 w-full">
                            <label for="member_email"
                                class="hidden mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Email
                                address</label>
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">

                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                    viewBox="0 0 24 24" id="hastag" data-name="Flat Line"
                                    xmlns="http://www.w3.org/2000/svg" class="icon flat-line">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path id="primary" d="M5,9H20M4,15H19M17.52,3,12.69,21M6.48,21,11.31,3"
                                            style="fill: none; stroke: #6B7280 ; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;">
                                        </path>
                                    </g>
                                </svg>
                            </div>
                            <input
                                class="block p-3 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Masukan kode transaksi anda" name="transaction_id" id="member_email"
                                required="">
                        </div>
                        <div>
                            <button type="submit" value="Cari"
                                class="py-3 px-5 text-sm font-medium text-center text-white rounded-lg cursor-pointer bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                name="member_submit" id="member_submit">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Block end -->

    <!-- Main modal -->
    <div id="ErrorModal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 ">
        <div class="relative p-4 w-full max-w-xl">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-end mb-4 rounded-t sm:mb-5">
                    <button type="button" id="closeModalButton"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="flex flex-col items-center text-center">
                    <img src="/img/kucing-cape.png" alt="Kucing Cape" class="w-full mb-4">
                    <h2 class="font-bold text-2xl text-gray-900 dark:text-white mb-2">Mohon Maaf</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-2">Trx id tidak ditemukan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction detail section - you would need to dynamically populate this based on your data -->
    <div class="bg-primary-100 dark:bg-gray-800 w-full py-8">
        <div class="px-4 mx-auto max-w-screen-sm text-center lg:px-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Track the delivery of order
                #957684673</h2>
        </div>
    </div>
    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mt-6 sm:mt-8 lg:flex lg:gap-8">
                <div
                    class="w-full divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200 dark:divide-gray-700 dark:border-gray-700 lg:max-w-xl xl:max-w-2xl">
                    <div class="space-y-4 p-6">
                        <div class="flex items-center gap-6">
                            <a href="#" class="h-14 w-14 shrink-0">
                                <img class="h-full w-full dark:hidden"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg"
                                    alt="imac image" />
                                <img class="hidden h-full w-full dark:block"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg"
                                    alt="imac image" />
                            </a>
                            <a href="#"
                                class="min-w-0 flex-1 font-medium text-gray-900 hover:underline dark:text-white"> PC
                                system All in One APPLE iMac (2023) mqrq3ro/a, Apple M3, 24" Retina 4.5K, 8GB, SSD
                                256GB, 10-core GPU, macOS Sonoma, Blue, Keyboard layout INT </a>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400"><span
                                    class="font-medium text-gray-900 dark:text-white">Product ID:</span> BJ8364850</p>
                            <div class="flex items-center justify-end gap-4">
                                <p class="text-base font-normal text-gray-900 dark:text-white">x1</p>
                                <p class="text-xl font-bold leading-tight text-gray-900 dark:text-white">$1,499</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 p-6">
                        <div class="flex items-center gap-6">
                            <a href="#" class="h-14 w-14 shrink-0">
                                <img class="h-full w-full dark:hidden"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/apple-watch-light.svg"
                                    alt="phone image" />
                                <img class="hidden h-full w-full dark:block"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/apple-watch-dark.svg"
                                    alt="phone image" />
                            </a>
                            <a href="#"
                                class="min-w-0 flex-1 font-medium text-gray-900 hover:underline dark:text-white">
                                Restored Apple Watch Series 8 (GPS) 41mm Midnight Aluminum Case with Midnight Sport Band
                            </a>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400"><span
                                    class="font-medium text-gray-900 dark:text-white">Product ID:</span> BJ8364850</p>
                            <div class="flex items-center justify-end gap-4">
                                <p class="text-base font-normal text-gray-900 dark:text-white">x2</p>
                                <p class="text-xl font-bold leading-tight text-gray-900 dark:text-white">$598</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 p-6">
                        <div class="flex items-center gap-6">
                            <a href="#" class="h-14 w-14 shrink-0">
                                <img class="h-full w-full dark:hidden"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ps5-light.svg"
                                    alt="console image" />
                                <img class="hidden h-full w-full dark:block"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ps5-dark.svg"
                                    alt="console image" />
                            </a>
                            <a href="#"
                                class="min-w-0 flex-1 font-medium text-gray-900 hover:underline dark:text-white"> Sony
                                Playstation 5 Digital Edition Console with Extra Blue Controller, White PULSE 3D Headset
                                and Surge Dual Controller </a>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400"><span
                                    class="font-medium text-gray-900 dark:text-white">Product ID:</span> BJ8364850</p>
                            <div class="flex items-center justify-end gap-4">
                                <p class="text-base font-normal text-gray-900 dark:text-white">x1</p>
                                <p class="text-xl font-bold leading-tight text-gray-900 dark:text-white">$799</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 p-6">
                        <div class="flex items-center gap-6">
                            <a href="#" class="h-14 w-14 shrink-0">
                                <img class="h-full w-full dark:hidden"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/xbox-light.svg"
                                    alt="xbox image" />
                                <img class="hidden h-full w-full dark:block"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/xbox-dark.svg"
                                    alt="xbox image" />
                            </a>
                            <a href="#"
                                class="min-w-0 flex-1 font-medium text-gray-900 hover:underline dark:text-white"> Xbox
                                Series X Diablo IV Bundle + 2 Xbox Wireless Controller Carbon Black + Controller Charger
                            </a>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400"><span
                                    class="font-medium text-gray-900 dark:text-white">Product ID:</span> BJ8364850</p>
                            <div class="flex items-center justify-end gap-4">
                                <p class="text-base font-normal text-gray-900 dark:text-white">x1</p>
                                <p class="text-xl font-bold leading-tight text-gray-900 dark:text-white">$699</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 p-6">
                        <div class="flex items-center gap-6">
                            <a href="#" class="h-14 w-14 shrink-0">
                                <img class="h-full w-full dark:hidden"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/iphone-light.svg"
                                    alt="phone image" />
                                <img class="hidden h-full w-full dark:block"
                                    src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/iphone-dark.svg"
                                    alt="phone image" />
                            </a>
                            <a href="#"
                                class="min-w-0 flex-1 font-medium text-gray-900 hover:underline dark:text-white"> APPLE
                                iPhone 15 5G phone, 256GB, Gold </a>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400"><span
                                    class="font-medium text-gray-900 dark:text-white">Product ID:</span> BJ8364850</p>
                            <div class="flex items-center justify-end gap-4">
                                <p class="text-base font-normal text-gray-900 dark:text-white">x3</p>
                                <p class="text-xl font-bold leading-tight text-gray-900 dark:text-white">$2,997</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 bg-gray-50 p-6 dark:bg-gray-800">
                        <div class="space-y-2">
                            <dl class="flex items-center justify-between gap-4">
                                <dt class="font-normal text-gray-500 dark:text-gray-400">Original price</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">$6,592.00</dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-4">
                                <dt class="font-normal text-gray-500 dark:text-gray-400">Savings</dt>
                                <dd class="text-base font-medium text-green-500">-$299.00</dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-4">
                                <dt class="font-normal text-gray-500 dark:text-gray-400">Store Pickup</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">$99</dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-4">
                                <dt class="font-normal text-gray-500 dark:text-gray-400">Tax</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">$799</dd>
                            </dl>
                        </div>
                        <dl
                            class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                            <dt class="text-lg font-bold text-gray-900 dark:text-white">Total</dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-white">$7,191.00</dd>
                        </dl>
                    </div>
                </div>

                <div class="mt-6 grow sm:mt-8 lg:mt-0">
                    <div
                        class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Order history</h3>
                        <ol class="relative ms-3 border-s border-gray-200 dark:border-gray-700">
                            <li class="mb-10 ms-6">
                                <span
                                    class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 ring-8 ring-white dark:bg-gray-700 dark:ring-gray-800">
                                    <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
                                    </svg>
                                </span>
                                <h4 class="mb-0.5 text-base font-semibold text-gray-900 dark:text-white">Estimated
                                    delivery in 24 Nov 2023</h4>
                                <p class="text-sm font-normal text-gray-500 dark:text-gray-400">Products delivered</p>
                            </li>
                            <li class="mb-10 ms-6">
                                <span
                                    class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 ring-8 ring-white dark:bg-gray-700 dark:ring-gray-800">
                                    <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                    </svg>
                                </span>
                                <h4 class="mb-0.5 text-base font-semibold text-gray-900 dark:text-white">Today</h4>
                                <p class="text-sm font-normal text-gray-500 dark:text-gray-400">Products being
                                    delivered</p>
                            </li>
                            <li class="mb-10 ms-6 text-primary-700 dark:text-primary-500">
                                <span
                                    class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                    </svg>
                                </span>
                                <h4 class="mb-0.5 font-semibold">23 Nov 2023, 15:15</h4>
                                <p class="text-sm">Products in the courier's warehouse</p>
                            </li>
                            <li class="mb-10 ms-6 text-primary-700 dark:text-primary-500">
                                <span
                                    class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                    </svg>
                                </span>
                                <h4 class="mb-0.5 text-base font-semibold">22 Nov 2023, 12:27</h4>
                                <p class="text-sm">Products delivered to the courier - DHL Express</p>
                            </li>
                            <li class="mb-10 ms-6 text-primary-700 dark:text-primary-500">
                                <span
                                    class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                    </svg>
                                </span>
                                <h4 class="mb-0.5 font-semibold">19 Nov 2023, 10:47</h4>
                                <p class="text-sm">Payment accepted - VISA Credit Card</p>
                            </li>
                            <li class="ms-6 text-primary-700 dark:text-primary-500">
                                <span
                                    class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                    </svg>
                                </span>
                                <div>
                                    <h4 class="mb-0.5 font-semibold">19 Nov 2023, 10:45</h4>
                                    <a href="#" class="text-sm font-medium hover:underline">Order placed -
                                        Receipt #647563</a>
                                </div>
                            </li>
                        </ol>
                        <div class="gap-4 sm:flex sm:items-center">
                            <button type="button"
                                class="w-full rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">Cancel
                                the order</button>
                            <a href="#"
                                class="mt-4 flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 sm:mt-0">Order
                                details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Rest of your transaction detail HTML would go here -->
    <!-- You would need to dynamically populate this section with actual transaction data -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('ErrorModal');
            const closeModalButton = document.getElementById('closeModalButton');

            // Example of how to show modal when needed
            // modal.classList.remove('hidden');
            // modal.setAttribute('aria-hidden', 'false');

            // Function to close modal
            closeModalButton.addEventListener('click', function() {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            });

            // Close modal when clicking outside
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        });
    </script>
</div>
