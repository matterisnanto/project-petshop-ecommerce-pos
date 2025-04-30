<div>
    <section class="bg-gray-50 py-2 antialiased dark:bg-gray-900 md:py-4">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <!-- Heading & Filters -->
            <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                {{-- <div>
                    <livewire:navigation.bread-crumb :links="[['text' => 'Home', 'url' => route('home')]]" currentPage="All Animals" />
                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
                        All Animals</h2>
                </div> --}}
                <div>
                    <livewire:navigation.bread-crumb :links="[['text' => 'Home', 'url' => route('animals')]]" :currentPage="$categoryAnimalName" />

                    <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
                        {{ $categoryAnimalName }}
                    </h2>
                </div>
                <div
                    class="sm:flex sm:items-center sm:space-x-4 mb-4 grid gap-4 grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4">
                    <button data-modal-toggle="filterModal" data-modal-target="filterModal" type="button"
                        class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto">
                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                        </svg>
                        Filters
                        <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <button id="sortDropdownButton1" data-dropdown-toggle="dropdownSort1" type="button"
                        class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto">
                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                        </svg>
                        @switch($currentSort)
                            @case('default')
                                Default
                            @break

                            @case('popular')
                                Most Popular
                            @break

                            @case('price_asc')
                                Price: Low to High
                            @break

                            @case('price_desc')
                                Price: High to Low
                            @break
                        @endswitch
                        <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="dropdownSort1"
                        class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                        data-popper-placement="bottom">
                        <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400"
                            aria-labelledby="sortDropdownButton">
                            <li>
                                <a href="#" wire:click.prevent="sort('default')"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Default </a>
                            </li>
                            <li>
                                <a href="#" wire:click.prevent="sort('popular')"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    The most popular </a>
                            </li>
                            <li>
                                <a href="#" wire:click.prevent="sort('price_asc')"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Increasing price </a>
                            </li>
                            <li>
                                <a href="#" wire:click.prevent="sort('price_desc')"
                                    class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Decreasing price </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- list animals --}}
            <div class="mb-4 grid gap-4 grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4">
                @foreach ($animals as $animal)
                    <div
                        class="w-full rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                        <div class="h-30 w-full md:h-56">
                            <a wire:navigate href="/animals/{{ $animal->slug }}">
                                <img class="mx-auto h-full dark:hidden"
                                    src="{{ $animal->thumbnail ?: 'https://via.placeholder.com/300' }}"
                                    alt="{{ $animal->name }}" />
                                <img class="mx-auto hidden h-full dark:block"
                                    src="{{ $animal->thumbnail ?: 'https://via.placeholder.com/300' }}"
                                    alt="{{ $animal->name }}" />
                            </a>
                        </div>
                        <div class="pt-2 md:pt-6">
                            <a wire:navigate href="/animals/{{ $animal->slug }}"
                                class="block text-sm font-semibold leading-tight text-gray-900 hover:underline dark:text-white md:text-lg whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $animal->name }}
                            </a>
                            <ul class="sm:mt-2 flex flex-nowrap items-center gap-1 md:gap-4">
                                <li class="flex shrink-0 items-center gap-1 md:gap-2">
                                    <svg class="h-3 w-3 shrink-0 md:h-4 md:w-4 text-gray-500 dark:text-gray-400"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                    </svg>
                                    <p class="truncate text-xs font-medium text-gray-500 dark:text-gray-400 md:text-sm">
                                        Fast Delivery</p>
                                </li>

                                <li class="flex shrink-0 items-center gap-1 md:gap-2">
                                    <svg class="h-3 w-3 shrink-0 md:h-4 md:w-4 text-gray-500 dark:text-gray-400"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                            d="M8 7V6c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1h-1M3 18v-7c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                    </svg>
                                    <p
                                        class="truncate text-xs font-medium text-gray-500 dark:text-gray-400 md:text-sm">
                                        Best Price</p>
                                </li>
                            </ul>

                            <div class="mt-1 sm:mt-2 flex items-center justify-between gap-2 md:gap-4 md:mt-4">
                                <p
                                    class="text-sm sm:text-lg font-extrabold leading-tight text-gray-900 dark:text-white md:text-xl">
                                    {{ format_currency($animal->selling_price) }}</p>

                                {{-- <button type="button" wire:click="addToCart('{{ $animal->id }}')"
                                    class="inline-flex items-center rounded-lg bg-primary-500 px-2 py-1 text-xs font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-600 dark:focus:ring-primary-800 md:px-2 md:py-1.5 md:text-sm">
                                    <svg class="-ms-1 me-1 h-3 w-3 md:h-5 md:w-5" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                    </svg>
                                    <span class="hidden md:inline">Add to cart</span>
                                    <span class="md:hidden">Add</span>
                                </button> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- end list animals --}}

            <!-- Pagination -->
            <div class="flex justify-center mt-6">
                {{ $animals->links() }}
            </div>

        </div>
        <!-- Filter modal -->
        <div id="filterModal" wire:ignore.self tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0 md:h-full">
            <div class="relative h-full w-full max-w-xl md:h-auto">
                <!-- Modal content -->
                <div class="relative rounded-lg bg-white shadow dark:bg-gray-800">
                    <!-- Modal header -->
                    <div class="flex items-start justify-between rounded-t p-4 md:p-5">
                        <h3 class="text-lg font-normal text-gray-500 dark:text-gray-400">Filters</h3>
                        <button type="button"
                            class="ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="filterModal">
                            <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="px-2 md:px-5">
                        <div class=" border-b border-gray-200 dark:border-gray-700">
                            <ul class="-mb-px flex flex-wrap text-center text-sm font-medium" id="myTab"
                                data-tabs-toggle="#myTabContent" role="tablist">
                                <li class="mr-1" role="presentation">
                                    <button
                                        class="inline-block border-b-2 border-transparent px-2 pb-2 pr-1 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                        id="breed-tab" data-tabs-target="#breed" type="button" role="tab"
                                        aria-controls="breed" aria-selected="true">Breeds</button>
                                </li>
                                <li class="mr-1" role="presentation">
                                    <button
                                        class="inline-block border-b-2 border-transparent px-2 pb-2 pr-1 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                        id="category-tab" data-tabs-target="#category" type="button" role="tab"
                                        aria-controls="category" aria-selected="false">Category</button>
                                </li>
                                <li class="mr-1" role="presentation">
                                    <button
                                        class="inline-block border-b-2 border-transparent px-2 pb-2 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                        id="price-tab" data-tabs-target="#price" type="button" role="tab"
                                        aria-controls="price" aria-selected="false">Price Range</button>
                                </li>
                            </ul>
                        </div>
                        <div id="myTabContent">
                            <!-- Breed Tab -->
                            <div class="grid-cols-2 gap-4 md:grid-cols-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                                id="breed" role="tabpanel" aria-labelledby="breed-tab">
                                @foreach ($breeds->groupBy(function ($item) {
        return strtoupper(substr($item->name, 0, 1));
    }) as $initial => $breedGroup)
                                    <div class="space-y-2">
                                        <h5 class="text-lg font-medium uppercase text-black dark:text-white">
                                            {{ $initial }}</h5>
                                        @foreach ($breedGroup as $breed)
                                            <div class="flex items-center">
                                                <input id="breed-{{ $breed->id }}" type="checkbox"
                                                    wire:model="selectedBreeds" value="{{ $breed->id }}"
                                                    class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-primary-600" />
                                                <label for="breed-{{ $breed->id }}"
                                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    {{ $breed->name }} ({{ $breed->animals_count }})
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <!-- Category Tab -->
                            <div class="hidden grid-cols-2 gap-4 md:grid-cols-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                                id="category" role="tabpanel" aria-labelledby="category-tab">
                                @foreach ($categories->groupBy(function ($item) {
        return strtoupper(substr($item->name, 0, 1));
    }) as $initial => $categoryGroup)
                                    <div class="space-y-2">
                                        <h5 class="text-lg font-medium uppercase text-black dark:text-white">
                                            {{ $initial }}</h5>
                                        @foreach ($categoryGroup as $category)
                                            <div class="flex items-center">
                                                <input id="category-{{ $category->id }}" type="checkbox"
                                                    wire:model="selectedCategoryAnimal" value="{{ $category->id }}"
                                                    class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-primary-600" />
                                                <label for="category-{{ $category->id }}"
                                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    {{ $category->name }} ({{ $category->animals_count }})
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <!-- Price Range Tab -->
                            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="price"
                                role="tabpanel" aria-labelledby="price-tab">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 gap-8">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label for="min-price"
                                                    class="block text-sm font-medium text-gray-900 dark:text-white">Min
                                                    Price</label>
                                                <input id="min-price" type="range" min="0" max="5000000"
                                                    wire:model="minPrice"
                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" />
                                            </div>

                                            <div>
                                                <label for="max-price"
                                                    class="block text-sm font-medium text-gray-900 dark:text-white">Max
                                                    Price</label>
                                                <input id="max-price" type="range" min="0" max="5000000"
                                                    wire:model="maxPrice"
                                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200 dark:bg-gray-700" />
                                            </div>

                                            <div class="col-span-2 flex items-center justify-between space-x-2">
                                                <input type="number" id="min-price-input" wire:model="minPrice"
                                                    min="0" max="5000000"
                                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" />

                                                <div class="shrink-0 text-sm font-medium dark:text-gray-300">to</div>

                                                <input type="number" id="max-price-input" wire:model="maxPrice"
                                                    min="0" max="5000000"
                                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center space-x-4 rounded-b p-4 dark:border-gray-600 md:p-5">
                        <button type="button" wire:click="applyFilters" data-modal-hide="filterModal"
                            class="rounded-lg bg-primary-500 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-700 dark:hover:bg-primary-800 dark:focus:ring-primary-800">
                            Apply Filters
                        </button>
                        <button type="button" wire:click="resetFilters" data-modal-hide="filterModal"
                            class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                            Reset All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
