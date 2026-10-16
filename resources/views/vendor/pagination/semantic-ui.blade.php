@if ($paginator->hasPages())
    <nav class="ui pagination menu" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">&lt;</span>
        @else
            <a class="item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lt;</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="item disabled" aria-disabled="true">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="item active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="item" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&gt;</a>
        @else
            <span class="item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">&gt;</span>
        @endif
    </nav>
@endif
