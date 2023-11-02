@if (!$brands->isEmpty())
    <section class="brands">
        <div class="container">
            <div class="content-top">
                <h2><a href="{{ route('brands.index') }}" class="text-dark">{{ __('main.brands') }}</a></h2>
            </div>
            <div class="row brands-wrap">
                @foreach ($brands as $key => $brand)
                    <div class="col-md-2 col-4 brand-box__parent @if($key > 8) d-none d-md-block @endif">
                        <a href="{{ $brand->url }}" class="brand-box radius-6 shadow" title="{{ $brand->name }}">
                            <img src="{{ $brand->small_img }}" alt="{{ $brand->name }}" class="img-fluid">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
