@extends('layouts.store')

@section('title', 'Order Confirmation')

@section('content')
    <section class="bg-white py-8 p-2 antialiased dark:bg-gray-900 md:py-16">
        <div
            class="p-6 rounded-lg border border-gray-200 bg-white p-4 drop-shadow-sm dark:border-gray-700 dark:bg-gray-800 mx-auto max-w-4xl px-4 2xl:px-2">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl mb-2">Terima Kasih telah berbelanja di
                kami!</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6 md:mb-8">Orderan anda <a href="#"
                    class="font-medium text-gray-900 dark:text-white hover:underline">#7564804</a> akan diproses dalam waktu
                24 jam selama hari kerja. Kami akan memberi tahu Anda melalui email setelah pesanan Anda dikirim, dan mohon
                screenshot atau catat nomor Trx Id Anda untuk cadangan bila terjadi masalah</p>
            <div
                class="space-y-4 sm:space-y-2 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800 mb-6 md:mb-8">
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Trx Id</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">#46864687</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Tanggal</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">14 May 2024</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Metode Pembayaran</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">JPMorgan monthly installments</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Nama</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">Flowbite Studios LLC</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Alamat</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">34 Scott Street, San Francisco,
                        California, USA</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Nomor Telepon</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">+(123) 456 7890</dd>
                </dl>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('product') }}"
                    class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">Kembali
                    Berbelanja</a>
                {{-- <a href="#"
                    class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Kembali
                    Berbelanja</a> --}}
            </div>
        </div>
    </section>
@endsection
