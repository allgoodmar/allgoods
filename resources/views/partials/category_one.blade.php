<a href="{{ $category->url }}" class="sub-categories-box">
    <div class="sub-categories-box__header radius-6 shadow">
        <img src="{{ $category->small_img }}" alt="{{ $category->name }}" class="img-fluid">
    </div>
    <div class="sub-categories-box__body">
        <strong>{{ $category->name }}</strong>
    </div>
</a>
