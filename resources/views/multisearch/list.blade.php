
<ul class="list-group p-3 list-group list-group-flush">
    {{--
        <hr>
    <h3>Qdiruv tarixi</h3>
    <hr>
    @foreach ($search['history'] as $item)
        <li>
            {{ $item }}
        </li>
    @endforeach
    --}}

    <div class="row">
        <div class="col-6 align-self-start"><h5 class="ml-3">{{ __("main.products") }}</h5></div>
        <div class="col-6 align-self-end text-right"><h6 class="multisearchModalClose"><a class="mb-2 ml-2 multisearchModalClose text-danger">Закрыть</a></h6></div>
    </div>

    @foreach ($items['results']['items'] as $item)
        <li class="list-group-item" style="text-decoration: none;">
            <a href="{{ $item['url'] }}" target="_blank"> <i class="bi bi-search"></i> {{ $item['name'] }}</a>
        </li>
    @endforeach

    <h5 class="ml-3 mt-3">{{ __("main.categories") }}</h5>

    @foreach ($items['results']['categories'] as $item)
        <li class="list-group-item" style="text-decoration: none;">
            <a href="{{ $item['url'] }}"> <i class="bi bi-text-indent-left"></i> {{ $item['name'] }}</a>
        </li>
    @endforeach
</ul>

<script>
    $(function () {
        $('.multisearchModalClose').on('click', function(){
            $('.mysearchoneNewAll').val('');
            $('.multisearchContainer').removeClass('d-block');
        });
    })
</script>