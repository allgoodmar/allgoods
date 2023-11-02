<div class="news-one">
    <a href="{{ $publication->url }}">
        <div class="news-one-img">
            <img src="{{ $publication->medium_img }}" alt="{{ $publication->name }}" class="img-fluid">
        </div>
        <div class="news-one-content">
            <div class="news-one-date">{{ Helper::formatDate($publication->getModel()->created_at, true) }}</div>
            <h4 class="news-one-title">{{ Str::words($publication->name, 6) }}</h4>
            <div class="news-one-buttons">
                <span class="btn btn-outline-white">{{ __('main.view_more') }}</span>
            </div>
        </div>
    </a>
</div>
