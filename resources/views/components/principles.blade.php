<!-- START PRINCIPLES -->
<div class="">
    <div class="custom-container">
        <div class="shopping_info">
            <div class="row justify-content-center">
                @foreach ($principles as $key => $principle)
                    <div class="col-md-3">
                        <div class="icon_box icon_box_style2">
                            <div class="icon">
                                <img src="{{ $principle->img }}" class="img-fluid" alt="{{ $principle->name }}">
                            </div>
                            <div class="icon_box_content">
                                <h5>{{ $principle->name }}</h5>
                                <p>{{ $principle->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- END PRINCIPLES -->
