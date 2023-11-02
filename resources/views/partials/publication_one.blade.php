<a href="{{ $publication->url }}" class="article-item">
    <div class="article-item__body radius-6">
        <img src="{{ $publication->medium_img }}" alt="{{ $publication->name }}" class="img-fluid">
    </div>
    <div class="article-item__footer">
        <strong>{{ $publication->name }}</strong>
    </div>
</a>
