@if(!$categories->isEmpty())
<section class="categories">
    <div class="container">
        <div class="content-top">
            <h2><a href="{{ route('categories') }}" class="text-dark">{{ __('main.nav.catalog') }}</a></h2>
            <a href="{{ route('categories') }}" class="more-link" data-mobile-text="{{ __('main.all') }}">
                <span>{{ __('main.view_all') }}</span>
                <svg width="18" height="18" fill="#6b7279">
                    <use xlink:href="#arrow"></use>
                </svg>
            </a>
        </div>
        <div class="row categories-wrap">
            <div class="slide_manualy">
            @php
                $key = 0;
            @endphp
            @foreach ($ids as $id)
                @if (!empty($categories[$id]))
                    @php
                        $category = $categories[$id];
                    @endphp
                    <div class="col-lg-12_5 col-sm-3 col-4 categories-box__parent @if($key > 7) d-sm-none @endif">
                        <a href="{{ $category->url }}" class="categories-box radius-6">
                            <div class="categories-box__header">
                                <img src="{{ $category->small_img }}" alt="{{ $category->getTranslatedAttribute('name') }}" class="img-fluid">
                            </div>
                            <div class="categories-box__body">
                                <strong>{{ $category->getTranslatedAttribute('name') }}</strong>
                            </div>
                        </a>
                    </div>
                    @php
                        $key++;
                    @endphp
                @endif
            @endforeach
            </div>
        </div>
    </div>
</section>

@endif
