@extends('layouts.app')

@section('seo_title', $seoTitle)
@section('meta_description', $metaDescription)

{{--
@section('seo_title', $seoTitle)
@section('meta_description', $metaDescription)--}}
@section('meta_keywords', $metaKeywords)

@section('microdata')
    {!! $microdata !!}
@endsection

@section('content')
    <main class="main product-page-container">
        @include('product.partials.product_page_content')
    </main>

@endsection

@section('after_footer_blocks')
    <div class="modal fade" id="not-in-stock-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <h4 class="mb-3">
                            {{ __('main.product_not_in_stock') }}
                        </h4>
                        <button type="button" class="btn btn-secondary mb-2" data-dismiss="modal">
                            {{ __('main.continue_shopping') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script>
        let playlists = document.querySelectorAll('.playlist .details_info')
        let mainVideo = document.querySelector(".main_video iframe")

        playlists.forEach(iframe => {
            iframe.onclick = () =>{
                playlists.forEach(func=> func.classList.remove('active_video_play'))
                iframe.classList.add("active_video_play")
                if(iframe.classList.contains("active_video_play")){
                    let src = iframe.children[0].getAttribute('src');
                    mainVideo.src = src;
                }
            }
        });

        $(function () {

            function makeTimer() {

                var sale_end_date = '{{ $product->sale_end_date }}';

                var endTime = new Date(sale_end_date);
                endTime = (Date.parse(endTime) / 1000);

                var now = new Date();
                now = (Date.parse(now) / 1000);

                var timeLeft = endTime - now;

                var days = Math.floor(timeLeft / 86400);
                var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
                var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600 )) / 60);
                var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));

                if (hours < "10") { hours = "0" + hours; }
                if (minutes < "10") { minutes = "0" + minutes; }
                if (seconds < "10") { seconds = "0" + seconds; }

                console.log("{{ Helper::formatPrice($product->price) }}");

                var currentdate = new Date();
                var datetime =
                    currentdate.getFullYear() + "-"
                    + ("0" + (currentdate.getMonth()+1)).slice(-2)  + "-"
                    + currentdate.getDate() + " "
                    + currentdate.getHours() + ":"
                    + ("0" + (currentdate.getMinutes())).slice(-2) + ":"
                    + ("0" + (currentdate.getSeconds())).slice(-2);

                    var options = {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Tashkent'
                    };

                    var currentDate = new Date();
                    var formattedDate = currentDate.toLocaleString('en-US', options).replace(/(\d+)\/(\d+)\/(\d+)/, '$3-$1-$2');
                    var CurrentformattedDateTime = formattedDate.replace(',', '');

                if (sale_end_date > CurrentformattedDateTime) {
                    $(".countdown-sale").html("<i class='bi bi-clock'></i> {{ __('main.left') }} " + days + " {{ __('main.day') }} " + hours + ":" + minutes + ":" + seconds);
                }else{
                    if (sale_end_date) {
                        var id = "{{ $product->id }}";
                        $(".center-element").html("{{ Helper::formatPrice($product->price) }}");

                        $.ajax({
                            url: "/updatesaledate",
                            data: {
                                id
                            },
                            method: "post",
                            success: function (data) {
                                $(".remove-del").addClass("d-none");
                                $(".countdown-sale").addClass("d-none");
                                $(".center-element").addClass("text-center");
                                $(".center-element").html("{{ Helper::formatPrice($product->price) }}");
                            }
                        });
                    }
                }
            }

            var stop_discount_ends = '{{ $product->sale_end_date }}';

            var options2 = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Tashkent'
            };
            
            var currentDate2 = new Date();
            var formattedDate2 = currentDate2.toLocaleString('en-US', options2).replace(/(\d+)\/(\d+)\/(\d+)/, '$3-$1-$2');
            var CurrentformattedDateTime2 = formattedDate2.replace(',', '');

            makeTimer();
 
            if (stop_discount_ends > CurrentformattedDateTime2) {
                setInterval(function() { makeTimer(); }, 1000);
            }

            let productID = $('.product-page-content').data('product-id');

            // theme specific
            $('body').on('click', '.show-all-specifications-btn', function () {
                $('[href="#product-characteristics"]').trigger('click');
                $("html, body").animate({
                    scrollTop: $('#product-descr-container').offset().top - 100,
                }, 600);
            })

            $(document).on('mouseenter', '.product-group-option-img', function(){
                let src = $(this).attr('src');
                let destImg = $('.product-preview__swiper .swiper-slide-active img');
                if (!destImg.length) {
                    destImg = $('.product-preview__swiper .swiper-slide').eq(0).find('img');
                }
                let destImgSrc = destImg.attr('src');
                destImg.attr('data-original-src', destImgSrc).attr('src', src);
            });
            $(document).on('mouseleave', '.product-group-option-img', function(){
                let destImg = $('.product-preview__swiper .swiper-slide-active img');
                destImg.attr('src', destImg.attr('data-original-src'));
            });
            initPreviewSlider()
            function initPreviewSlider() {
                let productSwiperThumbs = new Swiper('.product-preview__thumbs', {
                    direction: 'vertical',
                    slidesPerView: 5,
                    spaceBetween: 10,
                    freeMode: true,
                    watchSlidesVisibility: true,
                    watchSlidesProgress: true,
                    grabCursor: true
                })
                new Swiper('.product-preview__swiper', {
                    spaceBetween: 10,
                    thumbs: {
                    swiper: productSwiperThumbs
                    },
                    pagination: {
                    el: '.product-preview__swiper .swiper-pagination'
                    }
                })
            }
            // end theme specific

            // installment partners
            if ($('.select-partner-installment').length) {
                // applyPartnerInstallment($('.select-partner-installment').eq(0));
                $('body').on('change', '.select-partner-installment', function(){
                    applyPartnerInstallment($(this));
                });
                $('body').on('click', '.select-partner-installment option', function(){
                    applyPartnerInstallment($(this).parent());
                });
                // $('.partner-installment-block').on('click', function(){
                //     applyPartnerInstallment($(this).find('.select-partner-installment'));
                // })
                function applyPartnerInstallment(selectObj) {
                    let selectedOption = selectObj.find('option:selected');
                    let partnerBlock = selectObj.closest('.partner-installment-block');
                    let partnersListBlock = selectObj.closest('.partner-installments-list');
                    // let partnerAddToCartBtn = partnersListBlock.find('.add-to-cart-btn');
                    let partnerAddToCartBtn = partnerBlock.find('.add-to-cart-btn');
                    partnerAddToCartBtn.attr('data-checkout-url', selectedOption.data('checkout-url'));
                    if ($('.product-page-add-to-cart-btn').hasClass('disabled')) {
                        partnerAddToCartBtn.addClass('disabled').prop('disabled', true);
                    }
                    partnerBlock.find('.partner-installment-price').text(selectedOption.data('price-per-month-formatted'));
                    // $('.partner-installment-block').removeClass('active');
                    // partnerBlock.addClass('active');
                }
            }


            // product group
            // set proper attribute values
            if ($('.product-group-item').length) {
                // setCheckedAttributeValues();
                updateAvailableAttributeValues();
            }
            $('body').on('click', '.product-group-attribute-value-label', function(){
                let input = $('#' + $(this).attr('for'));
                if (input.prop('checked')) {
                    input.prop('checked', false);
                    updateAvailableAttributeValues();
                    return false;
                }
            })
            $('body').on('change', '.product-group-attribute-value-input', function(){
                let activeValues = $('.product-group-attribute-value-input:checked');
                let activeValuesIDs = [];
                activeValues.each(function(){
                    activeValuesIDs.push(+$(this).val());
                });
                let combination = activeValuesIDs.sort((a,b) => a-b).join('-');
                let productGroupItem = $('.product-group-item[data-combination="' + combination + '"]');
                if (!productGroupItem.length) {
                    updateAvailableAttributeValues();
                    return;
                }
                $.ajax({
                    url: productGroupItem.data('url'),
                    dataType: 'json',
                    beforeSend: function () {
                        $('.page-preloader').addClass('active');
                    }
                })
                    .done(function(data){
                        $('.product-page-container').html(data.main);
                        $('title').text(data.seo_title);
                        $('meta[name="description"]').text(data.meta_description);
                        $('meta[name="keywords"]').text(data.meta_keywords);
                        history.replaceState({
                            id: 'product-' + data.product_id,
                            source: 'web'
                        }, data.seo_title, data.product_url);
                        updateAvailableAttributeValues();
                        initPreviewSlider();
                        $('[data-toggle="tooltip"]').tooltip();
                    })
                    .always(function(){
                        $('.page-preloader').removeClass('active');
                    });
            });
            function setCheckedAttributeValues() {
                let activeProductGroupItem = $('.product-group-item[data-product-id="' + productID + '"]');
                let combination = activeProductGroupItem.data('combination');
                let attributeValueIDs = combination.split('-').map(value => +value);
                for (let attributeValueID of attributeValueIDs) {
                    $('.product-group-attribute-value-input[value="' + attributeValueID + '"]').prop('checked', true);
                }
            }
            function updateAvailableAttributeValues() {
                let attributes = $('.product-group-attribute');
                if (attributes.length == 1) {
                    let someInputChecked = false;
                    let currentAttribute = attributes.eq(0);
                    let currentAttributeInputs = currentAttribute.find('.product-group-attribute-value-input:not(:checked)');
                    currentAttributeInputs.each(function(){
                        let productGroupItemFound = false;
                        let combination = +$(this).val();
                        let productGroupItem = $('.product-group-item[data-combination="' + combination + '"]');
                        if (productGroupItem.length && +productGroupItem.data('in-stock') > 0) {
                            productGroupItemFound = true;
                        }
                        productGroupItemFound ? $(this).prop('disabled', false) : $(this).prop('disabled', true);
                    })
                } else if (attributes.length == 2) {
                    let someInputChecked = false;
                    attributes.each(function(){
                        let currentAttribute = $(this);
                        let currentAttributeID = currentAttribute.data('attribute-id');
                        let selectedAttributeValue = currentAttribute.find('.product-group-attribute-value-input:checked');
                        if (!selectedAttributeValue.length) {
                            return;
                        }
                        let selectedAttributeValueID = +selectedAttributeValue.val();
                        someInputChecked = true;
                        let beingCheckedAttribute = $('.product-group-attribute:not([data-attribute-id="' + currentAttributeID + '"])');
                        let beingCheckedAttributeInputs = beingCheckedAttribute.find('.product-group-attribute-value-input:not(:checked)');
                        beingCheckedAttributeInputs.each(function(){
                            let productGroupItemFound = false;
                            let combination = [selectedAttributeValueID, +$(this).val()].sort((a,b) => a-b).join('-');
                            let productGroupItem = $('.product-group-item[data-combination="' + combination + '"]');
                            if (productGroupItem.length && +productGroupItem.data('in-stock') > 0) {
                                productGroupItemFound = true;
                            }
                            productGroupItemFound ? $(this).prop('disabled', false) : $(this).prop('disabled', true);
                        })
                    });
                    if (!someInputChecked) {
                        $('.product-group-attribute-value-input').prop('disabled', false);
                    }
                } else if (attributes.length == 3) {
                    let checkedAttributeValues = $('.product-group-attribute-value-input:checked');
                    if (checkedAttributeValues.length == 3) {
                        attributes.each(function() {
                            let attribute = $(this);
                            let attributeID = attribute.data('attribute-id');
                            let otherAttributesCheckedAttributeValuesIDs = [];
                            let otherAttributesCheckedAttributeValues = $('.product-group-attribute:not([data-attribute-id="' + attributeID + '"])').find('.product-group-attribute-value-input:checked');
                            otherAttributesCheckedAttributeValues.each(function(){
                                otherAttributesCheckedAttributeValuesIDs.push(+$(this).val());
                            })
                            let notCheckedValues = $(this).find('.product-group-attribute-value-input:not(:checked)');
                            notCheckedValues.each(function(){
                                let combination = [+$(this).val(), otherAttributesCheckedAttributeValuesIDs[0], otherAttributesCheckedAttributeValuesIDs[1]];
                                let productGroupItem = $('.product-group-item[data-combination="' + combination + '"]');
                                (productGroupItem.length && +productGroupItem.data('in-stock') > 0) ? $(this).prop('disabled', false) : $(this).prop('disabled', true);
                            })
                        })
                    } else if (checkedAttributeValues.length == 2) {
                        attributes.each(function() {
                            let attribute = $(this);
                            let attributeID = attribute.data('attribute-id');
                            let currentAttributeCheckedAttributeValue = attribute.find('.product-group-attribute-value-input:checked');
                            if (!currentAttributeCheckedAttributeValue.length) {
                                let otherAttributesCheckedAttributeValuesIDs = [];
                                let otherAttributesCheckedAttributeValues = $('.product-group-attribute:not([data-attribute-id="' + attributeID + '"])').find('.product-group-attribute-value-input:checked');
                                otherAttributesCheckedAttributeValues.each(function(){
                                    otherAttributesCheckedAttributeValuesIDs.push(+$(this).val());
                                })
                                let currentAttributeValues = attribute.find('.product-group-attribute-value-input');
                                currentAttributeValues.each(function(){
                                    let combination = [+$(this).val(), otherAttributesCheckedAttributeValuesIDs[0], otherAttributesCheckedAttributeValuesIDs[1]].sort((a,b) => a-b).join('-');
                                    let productGroupItem = $('.product-group-item[data-combination="' + combination + '"]');
                                    (productGroupItem.length && +productGroupItem.data('in-stock') > 0) ? $(this).prop('disabled', false) : $(this).prop('disabled', true);
                                })
                            } else {
                                let currentAttributeCheckedAttributeValueID = +currentAttributeCheckedAttributeValue.val();
                                let otherAttributes = $('.product-group-attribute:not([data-attribute-id="' + attributeID + '"])');
                                let otherAttributesCheckedAttributeValue = otherAttributes.find('.product-group-attribute-value-input:checked');
                                let otherAttributesCheckedAttributeValueID = +otherAttributesCheckedAttributeValue.val();
                                let currentAttributeNotCheckedValues = attribute.find('.product-group-attribute-value-input:not(:checked)');
                                currentAttributeNotCheckedValues.each(function(){
                                    let combination = [+$(this).val(), currentAttributeCheckedAttributeValueID, otherAttributesCheckedAttributeValueID].sort((a,b) => a-b).join('-');
                                    let productGroupItem = $('.product-group-item[data-combination="' + combination + '"]');
                                    (productGroupItem.length && +productGroupItem.data('in-stock') > 0) ? $(this).prop('disabled', false) : $(this).prop('disabled', true);
                                })
                            }
                        })
                    } else if (checkedAttributeValues.length == 1) {
                        attributes.each(function() {
                            let attribute = $(this);
                            let attributeID = attribute.data('attribute-id');
                            let currentAttributeCheckedAttributeValue = attribute.find('.product-group-attribute-value-input:checked');
                            if (currentAttributeCheckedAttributeValue.length) {
                                let currentAttributeCheckedAttributeValueID = +currentAttributeCheckedAttributeValue.val();
                                let otherAttributes = $('.product-group-attribute:not([data-attribute-id="' + attributeID + '"])');
                                otherAttributes.each(function(){
                                    let secondAttribute = $(this);
                                    let secondAttributeID = secondAttribute.data('attribute-id');
                                    let secondAttributeValues = secondAttribute.find('.product-group-attribute-value-input');
                                    secondAttributeValues.each(function(){
                                        let secondCurrentAttributeValue = $(this);
                                        let secondCurrentAttributeValueID = +secondCurrentAttributeValue.val();
                                        let productItemFound = false;
                                        let thirdAttribute = $('.product-group-attribute:not([data-attribute-id="' + attributeID + '"], [data-attribute-id="' + secondAttributeID + '"])');
                                        let thirdAttributeID = thirdAttribute.data('attribute-id');
                                        let thirdAttributeValues = thirdAttribute.find('.product-group-attribute-value-input');
                                        thirdAttributeValues.each(function(){
                                            let combination = [+$(this).val(), currentAttributeCheckedAttributeValueID, secondCurrentAttributeValueID].sort((a,b) => a-b).join('-');
                                            let productGroupItem = $('.product-group-item[data-combination="' + combination + '"]');
                                            if (productGroupItem.length && +productGroupItem.data('in-stock') > 0) {
                                                productItemFound = true;
                                            }
                                        })
                                        productItemFound ? $(this).prop('disabled', false) : $(this).prop('disabled', true);
                                    })
                                })
                            } else {
                                //
                            }
                        })
                    } else {
                        $('.product-group-attribute-value-input').prop('disabled', false);
                    }
                }
            }

        });

    </script>

    <script src="{{ asset('js/owl/owl.carousel.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('.owl-carousel').owlCarousel({
                loop:true,
                center: false,
                autoplay:true,
                slideSpeed: 300,
                dots: false,
                responsive:{
                    0:{
                        items:2
                    },
                    600:{
                        items:3
                    },
                    1000:{
                        items:6
                    }
                }
            });
        });
    </script>

@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css">
    <link rel="stylesheet" href="{{ asset('js/owl/assets/owl.theme.default.min.css') }}">

    <style>
        .product-card__parent{
            max-width: 212px;
            margin: 0 auto;
        }

        .container_playlist
        {
            position: relative;
            display: flex;
            gap: 18px;
        }
        .main_video
        {
            width:735px;
            height: 348px;
        }
        .main_video iframe
        {
            height: 100%;
            width: 100%;
            border-radius: 10px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            -ms-border-radius: 10px;
            -o-border-radius: 10px;
        }

        .playlist
        {
            width: 300px;
            height: 348px;
            overflow-x: scroll;
            background: #eee !important;
            padding: 10px;
            border-radius:10px;
            -webkit-border-radius:10px;
            -moz-border-radius:10px;
            -ms-border-radius:10px;
            -o-border-radius:10px;
        }

        .playlist::-webkit-scrollbar
        {
            width: 5px;
            height: 0 !important;
            background: #fff;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
        }

        .playlist::-webkit-scrollbar-thumb
        {
            background-color: #007bff;
            border-radius: 5px;
        }

        .playlist .details_info
        {
            display: flex;
            height: 100px;
            gap: 5px;
            margin-top: 5px;
            padding: 5px;
            cursor: pointer;
            border-radius: 10px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            -ms-border-radius: 10px;
            -o-border-radius: 10px;
            transition: background .5s;
            -webkit-transition: background .5s;
            -moz-transition: background .5s;
            -ms-transition: background .5s;
            -o-transition: background .5s;
        }
        .playlist .details_info:hover,
        .playlist .active_video_play
        {
            background: #fff;
        }

        .playlist .details_info img
        {
            height: 100%;
            width: 180px;
            object-fit: cover;
            min-width: 150px;
            max-width: 150px;
            border-radius: 5px;
        }

        .playlist .details_info h4
        {
            font-size: 14px !important;
            margin-right: 5px;
        }

        @media screen and (max-width: 992px) {

            .container_playlist
            {
                display: flex !important;
                flex-direction: column;
                gap: 8px;
            }

            .main_video
            {
                position: relative;
                padding-bottom: 56.25%;
                height: 0;
                width: 100%!important;
            }

            .main_video iframe
            {
                position: absolute;
                top: 0;
                left: 0;
                height: 100%!important;
                width: 100%!important;
                border-radius: 0 !important;
                -webkit-border-radius: 0 !important;
                -moz-border-radius: 0 !important;
                -ms-border-radius: 0 !important;
                -o-border-radius: 0 !important;
            }

            .playlist
            {
                display: flex;
                height: 100%;
                width: 100% !important;
                overflow: scroll;
                gap: 12px;
                background: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
                -webkit-border-radius: 0 !important;
                -moz-border-radius: 0 !important;
                -ms-border-radius: 0 !important;
                -o-border-radius: 0 !important;
            }

            .playlist .details_info
            {
                flex-direction: column;
                align-items: center;
                padding: 0 !important;
                border-radius: 0% !important;
                -webkit-border-radius: 0% !important;
                -moz-border-radius: 0% !important;
                -ms-border-radius: 0% !important;
                -o-border-radius: 0% !important;
                margin-top: 0;
            }
            .playlist .active_video_play img
            {
                border: 2px solid #007bff;
                padding: 0px !important;
            }

            .playlist .details_info img
            {
                height: 80px;
                border-radius: 0% !important;
            }

            .playlist .details_info h4
            {
                display: none;
                padding: 0;
                font-size: 14px !important;
            }
        }
    </style>
@endsection
