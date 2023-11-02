<div id="header-bottom-catalog" class="header-bottom-catalog" data-aos="fade" data-aos-once="true">
    <button type="button" class="btn btn-primary w-100 header-bottom-category-list-switch {{ $isOpen ? 'open' : '' }}">
        <span class="text-uppercase">{{ __('main.products_catalog') }}</span>
        <i class="fa fa-bars d-none d-lg-inline-block"></i>
        <i class="fa fa-times d-lg-none"></i>
    </button>
    <div class="header-bottom-category-list {{ $isOpen ? 'open' : '' }}">
        @foreach ($categories as $headerBottomCategory)
            <div class="media align-items-center header-bottom-category-list-item flex-wrap">
                <img src="{{ $headerBottomCategory->micro_icon_img }}" class="mr-1" alt="{{ $headerBottomCategory->name }}">
                <div class="media-body">
                    <a href="{{ $headerBottomCategory->url }}">
                        {{ $headerBottomCategory->name }}
                        @if (!$headerBottomCategory->children->isEmpty())
                            <i class="fa fa-angle-down d-lg-none"></i>
                        @endif
                    </a>
                </div>
                @if (!$headerBottomCategory->children->isEmpty())
                    <div class="header-bottom-category-sublist">
                        <ul>
                            @foreach ($headerBottomCategory->children as $headerBottomCategoryChild)
                                <li>
                                    <a href="{{ $headerBottomCategoryChild->url }}">{{ $headerBottomCategoryChild->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endforeach
        <div class="media align-items-center header-bottom-category-list-item">
            <img src="{{ asset('images/icons/brands-light.png') }}" class="mr-1" alt="{{ __('main.brands') }}">
            <div class="media-body">
                <a href="{{ route('brands.index') }}">{{ __('main.brands') }}</a>
            </div>
        </div>
        <div class="media align-items-center header-bottom-category-list-item">
            <img src="{{ asset('images/icons/sale.png') }}" class="mr-1" alt="{{ __('main.sale') }}">
            <div class="media-body">
                <a href="{{ route('sale') }}">{{ __('main.sale') }}</a>
            </div>
        </div>
        <div class="media align-items-center header-bottom-category-list-item">
            <img src="{{ asset('images/icons/promotion.png') }}" class="mr-1" alt="{{ __('main.promotions') }}">
            <div class="media-body">
                <a href="{{ route('promotional-products') }}">{{ __('main.promotions') }}</a>
            </div>
        </div>
    </div>
</div>
