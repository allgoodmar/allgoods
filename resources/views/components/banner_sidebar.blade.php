<div class="sale-banner">
    @if($banner->url) <a href="{{ $banner->url }}" class="hover_effect1" > @endif
        <img src="{{ $banner->img }}" alt="{{ $banner->name }}">
    @if($banner->url) </a> @endif
</div>
