<div class="star-rating disabled">
    @php
        $random = Str::random();
        $max = config('cms.rating');
    @endphp
    @for($i = $max; $i >= 1; $i--)
        {{-- <input id="star-{{ $i }}-{{ $random }}" type="radio" name="rating-{{ $random }}" value="{{ $i }}" @if($i == $rating) checked @endif>
        <label for="star-{{ $i }}-{{ $random }}"> --}} 
            {{-- <i class="fa fa-star" ></i> --}}
            <svg width="20" height="20" fill="#fea92e"><use xlink:href="#star"></use></svg>
        {{-- </label> --}}
        @endfor
</div>
{{-- <div class="ml-2">
    @if (isset($ratingCount))
        {{ __('main.reviews2') }}: {{ $ratingCount }}
    @endif
</div> --}}
