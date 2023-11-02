<div class="star-rating">
    @php
        $max = config('cms.rating');
    @endphp
	@for($i = $max; $i >= 1; $i--)
        <input id="rating-{{ $i }}" type="radio" name="rating" value="{{ $i }}" @if($i == $max) checked @endif>
        <label for="rating-{{ $i }}" title="">
            {{-- <i class="fa fa-star" ></i> --}}
            <svg width="20" height="20" fill="currentColor"><use xlink:href="#star"></use></svg>
        </label>
	@endfor
</div>
