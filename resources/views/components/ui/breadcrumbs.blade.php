@props(['crumbs'])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        @foreach($crumbs as $crumb)
            <li class="inline-flex items-center">
                @if(!$loop->first)
                    <svg class="w-5 h-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                @endif

                @if(isset($crumb['url']) && !$loop->last)
                    <a href="{{ $crumb['url'] }}"
                        class="text-sm font-medium text-slate-400 dark:text-white hover:text-slate-500 dark:hover:text-slate-500 transition-colors">
                        {{ $crumb['label'] }}
                    </a>
                @else
                    <span
                        class="text-sm font-medium text-slate-400 {{ !$loop->first ? 'ml-1' : '' }}">{{ $crumb['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>