@if(!empty($breadcrumbs))
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach($breadcrumbs->getItems() as $link)
                @if($link->isActive())
                    <li class="breadcrumb-item">
                        <a href="{{ $link->url }}">{{ $link->name }}</a>
                        @if(!$loop->last)
                            <svg width="16" height="16" fill="#999">
                                <use xlink:href="#arrow"></use>
                            </svg>
                        @endif
                    </li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">
                        <span>{{ $link->name }}</span>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
