@php
    // Gabungkan links dengan current page
    $allItems = array_merge($links, [['text' => $currentPage, 'url' => null]]);
@endphp

<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
        @foreach ($allItems as $item)
            <li class="flex items-center">
                @if (!$loop->first)
                    <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m9 5 7 7-7 7" />
                    </svg>
                @endif

                @if ($loop->last)
                    <span
                        class="ms-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ms-2 truncate max-w-[120px] md:max-w-none">
                        {{ $item['text'] }}
                    </span>
                @else
                    <a href="{{ $item['url'] }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white truncate max-w-[100px] md:max-w-none">
                        @if ($loop->first)
                            <svg class="me-2.5 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                            </svg>
                        @endif
                        {{ $item['text'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
