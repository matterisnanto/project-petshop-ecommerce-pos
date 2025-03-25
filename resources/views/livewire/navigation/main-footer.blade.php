<footer class="bg-gray-50 dark:bg-gray-800">
    <div class="p-4 py-6 mx-auto max-w-screen-xl md:p-8 lg:py-10">
        <div class="grid grid-cols-2 gap-8 lg:grid-cols-6">
            <!-- Logo and Description -->
            <div class="col-span-2">
                <a href="#"
                    class="flex items-center mb-2 text-2xl font-semibold text-gray-900 sm:mb-0 dark:text-white">
                    <svg class="mr-2 h-8" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- SVG logo paths -->
                        <path
                            d="M25.2696 13.126C25.1955 13.6364 24.8589 14.3299 24.4728 14.9328C23.9856 15.6936 23.2125 16.2264 22.3276 16.4114L18.43 17.2265C17.8035 17.3575 17.2355 17.6853 16.8089 18.1621L14.2533 21.0188C13.773 21.5556 13.4373 21.4276 13.4373 20.7075C13.4315 20.7342 12.1689 23.9903 15.5149 25.9202C16.8005 26.6618 18.6511 26.3953 19.9367 25.6538L26.7486 21.7247C29.2961 20.2553 31.0948 17.7695 31.6926 14.892C31.7163 14.7781 31.7345 14.6639 31.7542 14.5498L25.2696 13.126Z"
                            fill="url(#paint0_linear_11430_22515)" />
                        <!-- Other SVG paths -->
                    </svg>
                    Flowbite
                </a>
                <p class="my-4 font-light text-gray-500 dark:text-gray-400">
                    Flowbite is a open-source library of over 400+ web components and interactive elements built with
                    the utility classes from Tailwind CSS.
                </p>
                <ul class="flex mt-5 space-x-6">
                    @foreach ($socialLinks as $social)
                        <li>
                            <a href="{{ $social['url'] }}"
                                class="text-gray-500 hover:text-gray-900 dark:hover:text-white dark:text-gray-400">
                                {!! $social['icon'] !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Company Links -->
            <div class="lg:mx-auto">
                <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Company</h2>
                <ul class="text-gray-500 dark:text-gray-400">
                    @foreach ($companyLinks as $link)
                        <li class="mb-4">
                            <a href="{{ $link['url'] }}" class="hover:underline">{{ $link['title'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Help Center Links -->
            <div class="lg:mx-auto">
                <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Help center</h2>
                <ul class="text-gray-500 dark:text-gray-400">
                    @foreach ($helpCenterLinks as $link)
                        <li class="mb-4">
                            <a href="{{ $link['url'] }}" class="hover:underline">{{ $link['title'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Legal Links -->
            <div class="lg:mx-auto">
                <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                <ul class="text-gray-500 dark:text-gray-400">
                    @foreach ($legalLinks as $link)
                        <li class="mb-4">
                            <a href="{{ $link['url'] }}" class="hover:underline">{{ $link['title'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Download Links -->
            <div class="lg:mx-auto">
                <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Download</h2>
                <ul class="text-gray-500 dark:text-gray-400">
                    @foreach ($downloadLinks as $link)
                        <li class="mb-4">
                            <a href="{{ $link['url'] }}" class="hover:underline">{{ $link['title'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8">

        <span class="block text-sm text-center text-gray-500 dark:text-gray-400">
            © {{ date('Y') }} <a href="#" class="hover:underline">Flowbite™</a>. All Rights Reserved.
        </span>
    </div>
</footer>
