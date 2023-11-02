@php
    if(empty($isCatalogOpen)) {
        $isCatalogOpen = 1;
    }
@endphp

<x-top-catalog :is-open="$isCatalogOpen"></x-top-catalog>

<x-day-product></x-day-product>

<x-discounted-products></x-discounted-products>

<x-banner-sidebar type="sidebar_1"></x-banner-sidebar>

@php
    $pageInstallments = \App\Page::find(8)->translate();
@endphp
<div class="mb-4" data-aos="fade" data-aos-once="true" data-aos-delay="200">
    <a href="{{ $pageInstallments->url }}" class="d-block">
        <img src="{{ asset('images/payment/zoodpay/sidebar-' . app()->getLocale() . '.jpg') }}" alt="Zoodpay" class="img-fluid rounded">
    </a>
</div>

