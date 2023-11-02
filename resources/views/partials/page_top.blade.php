{{-- <div class="page-top" @if(!empty($bg)) style="background-image: url({{ $bg }})" @endif>
    <div class="container">
        @include('partials.breadcrumbs')
        <h1 class="page-header">{{ $title ?? $page->title ?? '' }}</h1>
    </div>
    @include('partials.waves')
</div> --}}

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="custom-container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
        	<div class="col-md-6">
                <div class="page-title">
            		<h1>{{ $title ?? $page->title ?? '' }}</h1>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end">
                @include('partials.breadcrumbs')
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->
