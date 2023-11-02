@if ($paginator->hasPages())
    <ul class="pagination-list">

        @if ($paginator->onFirstPage())

        @else
            {{-- <a class="nav back" href="{{ $paginator->previousPageUrl() }}" rel="prev">назад</a> --}}
        @endif

        @foreach ($elements as $element)

            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li><span class="radius-6">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="radius-6 current">{{ $page }}</span></li>
                    @else
                        <li><a class="radius-6" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            {{-- <a class="nav forward" href="{{ $paginator->nextPageUrl() }}" rel="next">вперед</a> --}}
        @else

        @endif
    </ul>
@endif
