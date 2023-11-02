$(function () {
    "use strict";

    // /** add to home screen btn */
    // if ("serviceWorker" in navigator) {
    //     window.addEventListener("load", function () {
    //         navigator.serviceWorker.register("/sw.js").then(
    //             function (registration) {
    //                 // Registration was successful
    //                 console.log(
    //                     "ServiceWorker registration successful with scope: ",
    //                     registration.scope
    //                 );
    //             },
    //             function (err) {
    //                 // registration failed :(
    //                 console.log("ServiceWorker registration failed: ", err);
    //             }
    //         );
    //     });
    // }
    // let deferredPrompt;
    // const addToHomeScreenBtn = document.querySelector(
    //     ".add-to-home-screen-btn"
    // );
    // // console.log(addToHomeScreenBtn);
    // // addToHomeScreenBtn.style.display = "none";
    // window.addEventListener("beforeinstallprompt", (e) => {
    //     // Prevent Chrome 67 and earlier from automatically showing the prompt
    //     e.preventDefault();
    //     // Stash the event so it can be triggered later.
    //     deferredPrompt = e;
    //     // Update UI to notify the user they can add to home screen
    //     // addToHomeScreenBtn.style.display = "block";

    //     if (localStorage.getItem('doNotOfferPWA') == 1) {
    //         return;
    //     }

    //     $('#add-to-home-screen-modal').modal('show');

    //     addToHomeScreenBtn.addEventListener("click", (e) => {
    //         // hide our user interface that shows our A2HS button
    //         // addToHomeScreenBtn.style.display = "none";
    //         $('#add-to-home-screen-modal').modal('hide');

    //         // Show the prompt
    //         deferredPrompt.prompt();
    //         // Wait for the user to respond to the prompt
    //         deferredPrompt.userChoice.then((choiceResult) => {
    //             if (choiceResult.outcome === "accepted") {
    //                 console.log("User accepted the A2HS prompt");
    //             } else {
    //                 console.log("User dismissed the A2HS prompt");
    //             }
    //             deferredPrompt = null;
    //         });
    //     });
    // });
    // $('.dismiss-add-to-home-screen-btn').on('click', function(e){
    //     localStorage.setItem('doNotOfferPWA', 1);
    // });


    /* variables */
    let html = $("html");
    let body = $("body");

    let mousePageX, mousePageY, mouseClientX, mouseClientY;

    /* text to speech, speech synthesis */
    // let synth = window.speechSynthesis;
    // let utter = new SpeechSynthesisUtterance();
    // let speechLanguage = 'ru-RU1';
    // let currentActiveLangRegional = $('[name="active_language_regional"]');
    // if (currentActiveLangRegional.length) {
    //     speechLanguage = currentActiveLangRegional.val().replace('_', '-');
    // }
    // utter.lang = speechLanguage;
    // utter.volume = 0.7;

    /* set csrf token */
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    /* mousemove */
    $(document).on("mousemove", function (e) {
        mousePageX = e.pageX;
        mousePageY = e.pageY;
        mouseClientX = e.clientX;
        mouseClientY = e.clientY;
    });

    /* tooltips */
    $('[data-toggle="tooltip"]').tooltip();

    // captcha
    refreshCaptcha();

    /* search-form */
    let headerD = $('.header-d');
    let headerM = $('.header-m');
    let searchInputTimeout;
    let searchProcessing = false;
    let searchXHR;
    function hideAjaxSearch() {
        $('.ajax-search-results').removeClass('active');
        headerD.removeClass('fixed');
        headerM.removeClass('fixed');
    }
    body.on('click', function(e) {
        if (!$(e.target).closest('.ajax-search-results-content').length && !$(e.target).closest('.ajax-search-input').length) {
            hideAjaxSearch();
        }
    });

    $('.ajax-search-input').on('input focus', function(e){
        clearTimeout(searchInputTimeout);
        let input = $(this);
        if (input.val().length < 3) {
            hideAjaxSearch();
            return;
        }
        searchInputTimeout = setTimeout(() => {
            if (searchProcessing) {
                searchXHR.abort();
            }
            searchProcessing = true;
            let form = input.closest('form');
            let container = input.closest('.ajax-search-container');
            let results = container.find('.ajax-search-results')
            let btn = form.find('[type="submit"]');
            let btnHTML = btn.html();
            let sendUrl = form.attr('action');
            let sendData = form.serialize() + '&json=1';
            searchXHR = $.ajax({
                url: sendUrl,
                dataType: "json",
                data: sendData,
                beforeSend: function() {
                    btn.addClass("disabled").prop("disabled", true).html(spinnerHTML());
                }
            })
                .done(function(data) {
                    // console.log(data)
                    results.find('.list-group').empty();
                    if (data.brands.length || data.categories.length || data.products.length) {
                        if (data.brands.length) {
                            for (let i in data.brands) {
                                results.find('.brands-list-group').append('<a href="' + data.brands[i].url + '" class="list-group-item" title="' + data.brands[i].name + '"><img src="' + data.brands[i].small_img + '" alt="' + data.brands[i].name + '"> ' + data.brands[i].name + '</a>');
                                if (i >= 5) {
                                    break;
                                }
                            }
                        }
                        if (data.categories.length) {
                            for (let i in data.categories) {
                                results.find('.categories-list-group').append('<a href="' + data.categories[i].url + '" class="list-group-item" title="' + data.categories[i].full_name + '"><img src="' + data.categories[i].small_img + '" alt="' + data.categories[i].full_name + '"> ' + data.categories[i].full_name + '</a>');
                                if (i >= 5) {
                                    break;
                                }
                            }
                        }
                        if (data.products.length) {
                            for (let i in data.products) {
                                results.find('.products-list-group').append('<a href="' + data.products[i].url + '" class="list-group-item lh-125 d-flex" title="' + data.products[i].name + '"><img src="' + data.products[i].small_img + '" alt="' + data.products[i].name + '"><div><span class="d-inline-block mb-1">' + data.products[i].name + '</span><br><strong>' + data.products[i].current_price_formatted + '</strong></div></a>');
                                if (i >= 5) {
                                    break;
                                }
                            }
                        }
                        // results.append('<a href="' + sendUrl + '?q=' + input.val() + '" class="list-group-item">...</a>');
                        results.addClass('active');

                        headerD.addClass('fixed');
                        headerM.addClass('fixed');

                        // results container position
                        // if ($(window).width() >= 992) {
                        //     if ($('.header-d').hasClass('js-header-scroll')) {
                        //         results.css('top', '78px');
                        //     } else {
                        //         results.css('top', '124px');
                        //     }
                        // }
                    } else {
                        results.find('.list-group').empty();
                        results.removeClass('active');

                        headerD.removeClass('fixed');
                        headerM.removeClass('fixed');
                    }
                })
                .fail(function(data) {
                    // console.log(data);
                    results.find('.list-group').empty();
                    results.removeClass('active');

                    headerD.removeClass('fixed');
                    headerM.removeClass('fixed');
                })
                .always(function() {
                    searchProcessing = false;
                    btn.removeClass("disabled").prop("disabled", false).html(btnHTML);
                });
        }, 300);
    })

    /* bad eye form */
    let badEyeForm = $(".bad-eye-form");
    $(".btn-bad-eye").on("click", function (e) {
        e.preventDefault();
        let form = $(this).closest("form");
        let group = $(this).closest(".btn-group");
        let param = $(this).data("param");
        let value = $(this).data("value");
        group.find(".btn").removeClass("active");
        $(this).addClass("active");
        form.find("[name=" + param + "]").val(value);
        setTimeout(function () {
            form.submit();
        }, 300);
    });
    $(".set-normal-version").on("click", function (e) {
        e.preventDefault();
        let form = $(".bad-eye-form");
        form.find("input").val("normal");
        form.find(".btn-bad-eye").removeClass("active");
        setTimeout(function () {
            form.submit();
        }, 300);
    });
    badEyeForm.on("submit", function (e) {
        e.preventDefault();
        let form = $(this);
        let sendData = form.serialize();

        // set classes
        let fontSize = form.find("[name=font_size]").val();
        let contrast = form.find("[name=contrast]").val();
        let images = form.find("[name=images]").val();

        html.removeClass(
            "bad-eye-font_size-small bad-eye-font_size-normal bad-eye-font_size-large"
        );
        html.removeClass(
            "bad-eye-contrast-normal bad-eye-contrast-black_white bad-eye-contrast-white_black"
        );
        html.removeClass("bad-eye-images-normal bad-eye-images-disabled");

        if (
            fontSize == "normal" &&
            contrast == "normal" &&
            images == "normal"
        ) {
            html.removeClass("bad-eye");
        } else {
            html.addClass(
                "bad-eye bad-eye-font_size-" +
                    fontSize +
                    " bad-eye-contrast-" +
                    contrast +
                    " bad-eye-images-" +
                    images
            );
        }

        // save params
        $.ajax({
            url: form.attr("action"),
            method: "post",
            data: sendData,
        })
            .done(function (data) {
                // console.log(data);
            })
            .fail(function (data) {
                // console.log(data);
            });
    });
    // init bad-eye-form on start
    // badEyeForm.trigger('submit');
    /* end bad eye form */

    // custom start
    /* questions-list */
    $(".questions-list a").on("click", function (e) {
        e.preventDefault();
        $(this).toggleClass("active");
        $(this).next().toggleClass("active");
    });

    $('.category-filters-switch').on('click', function(){
        $('.catalog-sidebar').toggleClass('active');
    });
    $('body').on('click', function(e){
        if (!$(e.target).closest('.category-filters-switch').length && !$(e.target).closest('.catalog-sidebar').length) {
            $('.catalog-sidebar').removeClass('active');
        }
    });

    // custom end

    // phone input mask
    let phoneMaskElements = $(".phone-input-mask");
    let phoneMaskOptions = {
        mask: "+{998}00 000-00-00",
        lazy: false,
    };
    phoneMaskElements.each(function () {
        let element = $(this)[0];
        IMask(element, phoneMaskOptions);
    });

    // phone input mask 2
    let phoneMaskElements2 = $(".phone-input-mask2");
    let phoneMaskOptions2 = {
        mask: "+{998}00 000-00-00",
        lazy: false,
    };
    phoneMaskElements2.each(function () {
        let element2 = $(this)[0];
        IMask(element2, phoneMaskOptions2);
    });

    // card input mask 1
    let cardMaskElements = $(".card-input-mask");
    let cardMaskOptions = {
        mask: "0000 0000 0000 0000",
        lazy: false,
    };
    cardMaskElements.each(function () {
        let element1 = $(this)[0];
        IMask(element1, cardMaskOptions);
    });

    // card input mask 1
    let cardAvailableMaskElements = $(".card-available-input-mask");
    let cardAvailableMaskOptions = {
        mask: "00/00",
        lazy: false,
    };
    cardAvailableMaskElements.each(function () {
        let element11 = $(this)[0];
        IMask(element11, cardAvailableMaskOptions);
    });

    // card input mask 2
    let cardMaskElements2 = $(".card2-input-mask");
    let cardMaskOptions2 = {
        mask: "0000 0000 0000 0000",
        lazy: false,
    };
    cardMaskElements2.each(function () {
        let element2 = $(this)[0];
        IMask(element2, cardMaskOptions2);
    });

    // card input mask 2
    let cardAvailableMaskElements2 = $(".card-available2-input-mask");
    let cardAvailableMaskOptions2 = {
        mask: "00/00",
        lazy: false,
    };
    cardAvailableMaskElements2.each(function () {
        let element22 = $(this)[0];
        IMask(element22, cardAvailableMaskOptions2);
    });

    /* review-form */
    $(".review-form").on("submit", function (e) {
        e.preventDefault();
        let form = $(this);
        let btn = form.find("[type=submit]");
        let message = "";
        let formResultBlock = form.find(".form-result");
        let formHideBlock = form.find(".form-hide-blocks");

        $.ajax({
            method: form.attr("method"),
            url: form.attr("action"),
            dataType: "json",
            data: form.serialize(),
            beforeSend: function () {
                btn.addClass("disabled")
                    .prop("disabled", true)
                    .append(spinnerHTML());
                form.find(".alert").remove();
            },
        })
            .done(function (data) {
                message = `<div class="alert alert-success">
                            ${data.message}
                            </div>`;
                formResultBlock.html(message);
                // form.find('input, textarea').val('');
                formHideBlock.addClass("d-none");
            })
            .fail(function (data) {
                // console.log(data);
                if (data.status == 422) {
                    let result = data.responseJSON;
                    let messageContent = result.message + "<br>";
                    for (let i in result.errors) {
                        messageContent +=
                            "<span>" + result.errors[i] + "</span><br>";
                    }

                    message = `<div class="alert alert-danger">
                            ${messageContent}
                            </div>`;
                    formResultBlock.html(message);
                }
            })
            .always(function (data) {
                setTimeout(function () {
                    btn.removeClass("disabled")
                        .prop("disabled", false)
                        .find(".spinner")
                        .remove();
                }, 1000);
                refreshCaptcha();
            });
    });

    /* contact form */
    $(".contact-form").on("submit", function (e) {
        e.preventDefault();
        let form = $(this);
        let formHideBlock = form.find(".form-hide-blocks");
        let sendUrl = form.attr("action");
        let sendData = form.serialize();
        let button = form.find("[type=submit]");
        let message = "";
        $.ajax({
            url: sendUrl,
            method: "post",
            dataType: "json",
            data: sendData,
            beforeSend: function () {
                // clear message
                form.find(".form-result").empty();
                // disabel send button
                button
                    .addClass("disabled")
                    .prop("disabled", true)
                    .append(spinnerHTML());
            },
        })
            .done(function (data) {
                // console.log(data);
                form.find("input[type=text], input[type=email], textarea").val(
                    ""
                );
                message = `<div class="alert alert-success">
                            ${data.message}
                            </div>`;
                form.find(".form-result").html(message);
                formHideBlock.addClass("d-none");
                if (data.redirect_url) {
                    setTimeout(function () {
                        location.href = data.redirect_url;
                    }, 500);
                }
                // setTimeout(function(){
                //     location.reload();
                // }, 1000);
            })
            .fail(function (data) {
                // console.log(data);
                if (data.status == 422) {
                    let result = data.responseJSON;
                    let messageContent = result.message + "<br>";
                    for (let i in result.errors) {
                        messageContent +=
                            "<span>" + result.errors[i] + "</span><br>";
                    }

                    message = `<div class="alert alert-danger">
                            ${messageContent}
                            </div>`;
                    form.find(".form-result").html(message);
                }
            })
            .always(function () {
                // enable button
                button
                    .removeClass("disabled")
                    .prop("disabled", false)
                    .find(".spinner").remove();
                refreshCaptcha();
            });
    });
    $("#contact-modal").on("show.bs.modal", function (e) {
        let form = $(this).find("form");
        let button = $(e.relatedTarget);
        form.find(
            "[name=product_id], [name=category_id]"
        ).val("");
        if (button.data("product")) {
            form.find("[name=product_id]").val(button.data("product"));
        } else if (button.data("category")) {
            form.find("[name=category_id]").val(button.data("category"));
        }
    });

    /* subscriber form */
    $(".subscriber-form").on("submit", function (e) {
        e.preventDefault();
        let form = $(this);
        let sendUrl = form.attr("action");
        let sendData = form.serialize();
        let button = form.find("[type=submit]");
        let message = "";
        $.ajax({
            url: sendUrl,
            method: "post",
            dataType: "json",
            data: sendData,
            beforeSend: function () {
                // clear message
                form.find(".form-result").empty();
                // disabel send button
                button
                    .addClass("disabled")
                    .prop("disabled", true)
                    .append(spinnerHTML());
            },
        })
            .done(function (data) {
                form.find("input[type=text], input[type=email], textarea").val(
                    ""
                );
                message = `<div class="alert alert-success my-4">
                            ${data.message}
                            </div>`;
                form.find(".form-result").html(message);
            })
            .fail(function (data) {
                console.log(data);
                if (data.status == 422) {
                    let result = data.responseJSON;
                    let messageContent = result.message + "<br>";
                    for (let i in result.errors) {
                        messageContent +=
                            "<span>" + result.errors[i] + "</span><br>";
                    }

                    message = `<div class="alert alert-danger my-4">
                            ${messageContent}
                            </div>`;
                    form.find(".form-result").html(message);
                }
            })
            .always(function () {
                // enable button
                button
                    .removeClass("disabled")
                    .prop("disabled", false)
                    .find(".spinner")
                    .remove();
            });
    });

    // anchor smooth scroll
    //$(document).on('click', 'a[href^="#"]', function (e) {
    $(document).on("click", 'a.anchor[href^="#"]', function (e) {
        e.preventDefault();
        $("html, body").animate(
            {
                scrollTop: $($.attr(this, "href")).offset().top,
            },
            600
        );
    });

    /* theme scripts */

    /* text to speech btn */
    // let textToSpeechBtn = $('.text-to-speech-btn, .text-to-speech-float-btn');
    // body.on('click', '.text-to-speech-btn, .text-to-speech-float-btn', function(e){
    //     e.preventDefault();
    //     if (synth.speaking) {
    //         synth.cancel();
    //         $('.text-to-speech-float-btn').remove();
    //         return;
    //     }
    //     let text = $(this).data('text');
    //     // let selObj = window.getSelection();
    //     // let text = selObj.toString();
    //     if (text) {
    //         utter.text = text;
    //         $(this).find('.fa').addClass('pulsate-fwd');
    //         synth.speak(utter);
    //         utter.onend = function() {
    //             // alert('Speech has finished');
    //             $('.text-to-speech-float-btn').remove();
    //         }
    //     }
    // });

    // let textToSpeechBtnTimeout;
    // document.addEventListener('selectionchange', () => {
    //     clearTimeout(textToSpeechBtnTimeout);
    //     let selObj = window.getSelection();
    //     let text = selObj.toString();
    //     $('.text-to-speech-float-btn').remove();
    //     if (synth.speaking) {
    //         synth.cancel();
    //     }
    //     if (text) {
    //         let currentMouseClientX = mouseClientX;
    //         let currentMouseClientY = mouseClientY;
    //         textToSpeechBtnTimeout = setTimeout(function(){
    //             let btn = '<button class="btn btn-primary btn-round text-to-speech-float-btn" style="position: fixed; left: ' + currentMouseClientX + 'px; top: ' + currentMouseClientY + 'px; z-index: 1000;" data-text="' + text + '"><i class="fa fa-microphone"></i></button>';
    //             $(btn).appendTo(body);
    //         }, 500);
    //     }
    // });
    /* end text to speech btn */

    // // event after text has been spoken
    // utter.onend = function() {
    //     alert('Speech has finished');
    // }
    // // speak
    // synth.speak(utter);

	// BUY IN ONE CLICK STARTS HERE
        $('.add-to-cart-btn-prototype').on('click', function(){

            let btn = $(this);
            let loader = "add-spinner";

            let id = btn.attr("data-id");
            let name = btn.attr("data-name");
            let price = btn.attr("data-price");
            let quantity = +btn.attr("data-quantity");

var inst = btn.attr("data-inst");
        var uninstall = btn.attr("data-uninstall");
        var instPrice = btn.attr("data-instPrice");
        // var buyType = btn.attr("data-buytype");

        console.log("inst: " + inst);
        if (inst == "installment") {
            console.log("inst: " + inst);
            $.ajax({
                url: "/setsession",
                data: {
                    inst
                },
                method: "post",
                success: function (data) {
                    // return true;
                    // alert("failed to true session");
                }
            }).fail(function (data) {
                alert("failed to set session");
                return false;
            });
        } else {
            inst = "installment deleted";
            console.log("inst: " + inst);
            $.ajax({
                url: "/forgetsession",
                data: {
                    inst
                },
                method: "post",
                success: function (data) {
                    // alert("One");
                    // return true;
                }
            }).fail(function (data) {
                alert("failed to open");
                return false;
            });
        }

            if (quantity < 1) {
                quantity = 1;
            }

            if (!id || !name || !price || !quantity) {
                return false;
            }

            $.ajax({
            url: "/cart",
            data: {
                id,
                name,
                price,
                quantity,
            },
            method: "post",
	/*
            beforeSend: function () {
                btn.addClass("disabled")
                    .prop("disabled", true);
                if (loader == "flash-icon") {
                    // btn.find('.fa-heart').addClass('pulse');
                    btn.html(spinnerHTML());
                } else {
                    btn.append(spinnerHTML());
                }
            },*/
            })
            .done(function (data) {
                $("#buy-in-one-click").modal("show");
                $('#buy-in-one-click').modal({backdrop: 'static', keyboard: false});
                console.log(data);
                updateCartInfo(data.cart);
            })
            .fail(function (data) {
                console.log(data);
            })
            .always(function (data) {
                setTimeout(() => {
                    btn.removeClass("disabled").prop("disabled", false);
                    if (loader == "flash-icon") {
                        btn.html(btnHTML);
                    } else {
                        btn.find(".spinner").remove();
                    }
                }, 500);
            });
        });

        $('.add-to-cart-btn-prototype-url').on('click', function(){
            // $("#buy-in-one-click").modal("hide");
            location.href = btn.data('checkout-url');
        });

        // remove cart item prototype
        body.on("click", ".remove-from-cart-btn-prototype", function (e) {
            e.preventDefault();
            let btn = $(this);
            if (btn.hasClass("disabled")) {
                return false;
            }
            let url = btn.attr("href");

            $.ajax({
                url: url,
                data: {
                    _method: "DELETE",
                },
                method: "post"
            }).done(function (data) {
                // console.log(data);
                btn.closest(".cart_item_line").remove();
                updateCartInfo(data.cart);
            })
        });

        // BUY IN ONE CLICK ALMOST ENDS HERE (some codes are below)

        // place order btn
        $('#checkout-form').on('submit', function(e){
            let btn = $(this).find('[type="submit"]');
            btn
                .addClass('disabled')
                .append('<span class="spinner mx-1"><svg class="svg-spinner" width="24" height="24" viewBox="0 0 50 50"><circle class="svg-spinner-path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg></span>')
                // .prop('disabled', true);
        });
	
	/*BUY IN ONE CLICK ENDS*/

    /* cart */
    // add item to cart
    body.on("click", ".add-to-cart-btn", function (e) {
        e.preventDefault();
        let btn = $(this);
        let btnHTML = btn.html();
        let loader = "add-spinner";
        if (btn.hasClass("only-icon")) {
            loader = "flash-icon";
        }

        if (btn.hasClass("disabled")) {
            if ($(".product_page_in_stock").text() == "0") {
                $("#not-in-stock-modal").modal("show");
            }
            return false;
        }
        let id = btn.attr("data-id");
        let name = btn.attr("data-name");
        let price = btn.attr("data-price");
        let quantity = +btn.attr("data-quantity");

	let notspinner = btn.attr("data-notspinner");

        // console.log(quantity);
        if (quantity < 1) {
            quantity = 1;
        }

        // YANDEX METRICS
        /*
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "UZS",
                "detail": {
                    "products": [
                        {
                            "id": id,
                            "name" : name,
                            "price": price,
                            "quantity" : quantity
                        }
                    ]
                }
            }
        });*/

        // console.log(quantity);
        if (!id || !name || !price || !quantity) {
            return false;
        }
        $.ajax({
            url: "/cart",
            data: {
                id,
                name,
                price,
                quantity,
            },
            dataType: 'json',
            method: "post",
            beforeSend: function () {
                if (!notspinner) {
                    btn.addClass("disabled")
                    .prop("disabled", true);
                    if (loader == "flash-icon") {
                        // btn.find('.fa-heart').addClass('pulse');
                        btn.html(spinnerHTML());
                    } else {
                        btn.append(spinnerHTML());
                    }
                }
            },
        })
            .done(function (data) {
                // console.log(data);

                updateCartInfo(data.cart);
                if (btn.data('checkout-url')) {
                    location.href = btn.data('checkout-url');
                } else {
                    $("#cart-modal").modal("show");
                }
            })
            .fail(function (data) {
                if (data.status == 422) {
                    $('#info-modal .info-modal-content').text(data.responseJSON.message);
                    $('#info-modal').modal('show');
                }
                // console.log(data);
            })
            .always(function (data) {
                if (!notspinner) {
                    setTimeout(() => {
                        console.log(notspinner);
                        btn.removeClass("disabled").prop("disabled", false);
                        if (loader == "flash-icon") {
                            btn.html(btnHTML);
                        } else {
                            btn.find(".spinner").remove();
                        }
                    }, 500);
                }
            });
    });

    // update cart item
    let updateCartTimeout;
    body.on("change", ".update-cart-quantity-input", function (e) {
        clearTimeout(updateCartTimeout);
        updateCartTimeout = setTimeout(() => {
            let cartContainer = $(".cart_items_container");
            let input = $(this);
            let id = input.attr("data-id");
            let quantity = +input.val();

            if (!id || !quantity) {
                return false;
            }
            $.ajax({
                url: "/cart/update",
                data: {
                    id,
                    quantity,
                },
                method: "post",
                beforeSend: function () {
                    input.addClass("disabled").prop("disabled", true);
                    cartContainer.addClass("disabled");
                },
            })
                .done(function (data) {
                    // console.log(data);
                    updateCartInfo(data.cart);
                    input
                        .closest(".cart_item_line")
                        .find(".product_total")
                        .text(data.lineTotalFormatted);
                    input
                        .closest(".cart_item_line")
                        .find(".product_total_min_price_per_month")
                        .text(data.lineMinPricePerMonthFormatted);
                })
                .fail(function (data) {
                    // console.log(data);
                })
                .always(function (data) {
                    input.removeClass("disabled").prop("disabled", false);
                    cartContainer.removeClass("disabled");
                });
        }, 500);
    });

    // remove cart item
    body.on("click", ".remove-from-cart-btn", function (e) {
        e.preventDefault();
        let btn = $(this);
        if (btn.hasClass("disabled")) {
            return false;
        }
        let url = btn.attr("href");

        $.ajax({
            url: url,
            data: {
                _method: "DELETE",
            },
            method: "post",
            beforeSend: function () {
                btn.addClass("disabled")
                    .prop("disabled", true)
                    .empty()
                    .append(spinnerHTML());
            },
        })
            .done(function (data) {
                // console.log(data);
                btn.closest(".cart_item_line").remove();
                updateCartInfo(data.cart);
            })
            .fail(function (data) {
                // console.log(data);
            })
            .always(function (data) {
                setTimeout(() => {
                    btn.removeClass("disabled")
                        .prop("disabled", false)
                        .find(".spinner")
                        .remove();
                }, 500);
            });
    });

    function updateCartInfo(cart) {
        $(".cart_count").text(cart.quantity);
        $(".cart_total_price").text(cart.totalFormatted);
        $(".cart_min_price_per_month").text(cart.minPricePerMonthFormatted);
        $(".cart_standard_price_total").text(cart.standardPriceTotalFormatted);
        $(".cart_discount_price").text(cart.discountFormatted);
        if (cart.discount > 0) {
            $(".cart_discount_price_container").removeClass('d-none');
        } else {
            $(".cart_discount_price_container").addClass('d-none');
        }
        // toggleCartTotalMessages(cart.total);
    }

    function toggleCartTotalMessages(cartTotal) {
        let checkoutBtns = $(".checkout_btn a");
        let warningMessages = $(".order-amount-too-high-warning");
        let maxTotal =
            warningMessages.attr("data-max") != undefined
                ? +warningMessages.attr("data-max")
                : 50000000;
        if (checkoutBtns.length && warningMessages.length) {
            if (cartTotal > maxTotal) {
                checkoutBtns.addClass("disabled");
                warningMessages.removeClass("d-none");
            } else {
                checkoutBtns.removeClass("disabled");
                warningMessages.addClass("d-none");
            }
        }
    }
    /* end cart */

    /* wishlist */
    // add item to wishlist
    body.on("click", ".add-to-wishlist-btn", function (e) {
        e.preventDefault();
        let btn = $(this);
        if (btn.hasClass("disabled")) {
            return false;
        }
        let btnHTML = btn.html();
        let parentRow = btn.closest(".wishlist-tr-row");
        let url = btn.data("add-url");
        let id = btn.attr("data-id");
        let name = btn.attr("data-name");
        let price = btn.attr("data-price");
        let quantity = 1;
        let loader = "add-spinner";
        if (btn.hasClass("only-icon")) {
            loader = "flash-icon";
        }

        if (!id || !name || !price || !quantity) {
            return false;
        }
        $.ajax({
            url: url,
            data: {
                id,
                name,
                price,
                quantity,
            },
            method: "post",
            beforeSend: function () {
                // disable btn
                btn.addClass("disabled").prop("disabled", true);
                // add loader
                if (loader == "flash-icon") {
                    // btn.find('.fa-heart').addClass('pulse');
                    btn.html(spinnerHTML());
                } else {
                    btn.append(spinnerHTML());
                }
            },
        })
            .done(function (data) {
                updateWishlistInfo(data.wishlist);
                if (parentRow.length) {
                    parentRow.remove();
                } else {
                    btn.removeClass("add-to-wishlist-btn")
                        .addClass("remove-from-wishlist-btn")
                        .addClass("active")
                        .html(btn.attr("data-delete-text"));
                }
            })
            .fail(function (data) {
                console.log(data);
            })
            .always(function (data) {
                setTimeout(() => {
                    // enable btn
                    btn.removeClass("disabled").prop("disabled", false);
                    if (loader == "flash-icon") {
                        btn.html(btnHTML);
                    } else {
                        btn.find(".spinner").remove();
                    }
                }, 500);
            });
    });

    // remove wishlist item
    body.on("click", ".remove-from-wishlist-btn", function (e) {
        e.preventDefault();
        let btn = $(this);
        if (btn.hasClass("disabled")) {
            return false;
        }
        let btnHTML = btn.html();
        let url = btn.data("remove-url");
        let loader = "add-spinner";
        if (btn.hasClass("only-icon")) {
            loader = "flash-icon";
        }

        $.ajax({
            url: url,
            data: {
                _method: "DELETE",
            },
            method: "post",
            beforeSend: function () {
                // disable btn
                btn.addClass("disabled").prop("disabled", true);
                // add loader
                if (loader == "flash-icon") {
                    btn.html(spinnerHTML());
                } else {
                    btn.append(spinnerHTML());
                }
            },
        })
            .done(function (data) {
                // console.log(data);
                btn.closest('.wishlist_item_line').remove();
                updateWishlistInfo(data.wishlist);
                btn.removeClass("remove-from-wishlist-btn")
                    .addClass("add-to-wishlist-btn")
                    .removeClass("active")
                    .html(btn.attr("data-add-text"));
            })
            .fail(function (data) {
                // console.log(data);
            })
            .always(function (data) {
                setTimeout(() => {
                    // enable btn
                    btn.removeClass("disabled").prop("disabled", false);
                    if (loader == "flash-icon") {
                        btn.html(btnHTML);
                    } else {
                        btn.find(".spinner").remove();
                    }
                }, 500);
            });
    });

    function updateWishlistInfo(wishlist) {
        $(".wishlist_count").text(wishlist.quantity);
    }
    /* end wishlist */

    /* compare */
    // add item to compare
    body.on("click", ".add-to-compare-btn", function (e) {
        e.preventDefault();
        let btn = $(this);
        if (btn.hasClass("disabled")) {
            return false;
        }
        let btnHTML = btn.html();
        let url = btn.data("add-url");
        let id = btn.attr("data-id");
        let name = btn.attr("data-name");
        let price = btn.attr("data-price");
        let loader = "add-spinner";
        if (btn.hasClass("only-icon")) {
            loader = "flash-icon";
        }

        if (!id || !name || !price) {
            return false;
        }
        $.ajax({
            url: url,
            data: {
                id,
                name,
                price,
            },
            method: "post",
            beforeSend: function () {
                // disable btn
                btn.addClass("disabled").prop("disabled", true);
                // add loader
                if (loader == "flash-icon") {
                    btn.html(spinnerHTML());
                } else {
                    btn.append(spinnerHTML());
                }
            },
        })
            .done(function (data) {
                // console.log(data);
                updateCompareInfo(data.compare);
                btn.removeClass("add-to-compare-btn")
                    .addClass("remove-from-compare-btn")
                    .addClass("active")
                    .attr("title", btn.data("delete-text"));
            })
            .fail(function (data) {
                console.log(data);
            })
            .always(function (data) {
                setTimeout(() => {
                    // enable btn
                    btn.removeClass("disabled").prop("disabled", false);
                    if (loader == "flash-icon") {
                        btn.html(btnHTML);
                    } else {
                        btn.find(".spinner").remove();
                    }
                }, 500);
            });
    });

    // remove compare item
    body.on("click", ".remove-from-compare-btn", function (e) {
        e.preventDefault();
        let btn = $(this);
        if (btn.hasClass("disabled")) {
            return false;
        }
        let btnHTML = btn.html();
        let parentRow = btn.closest(".compare-row");
        let url = btn.data("delete-url");
        let loader = "add-spinner";
        if (btn.hasClass("only-icon")) {
            loader = "flash-icon";
        }

        $.ajax({
            url: url,
            data: {
                _method: "DELETE",
            },
            method: "post",
            beforeSend: function () {
                // disable btn
                btn.addClass("disabled").prop("disabled", true);
                // add loader
                if (loader == "flash-icon") {
                    btn.html(spinnerHTML());
                } else {
                    btn.append(spinnerHTML());
                }
            },
        })
            .done(function (data) {
                // console.log(data);
                if (parentRow.length) {
                    parentRow.remove();
                }
                updateCompareInfo(data.compare);
                btn.removeClass("remove-from-compare-btn")
                    .addClass("add-to-compare-btn")
                    .removeClass("active")
                    .attr("title", btn.data("add-text"));
                $('[data-compare-id="' + btn.data("id") + '"').remove();
            })
            .fail(function (data) {
                // console.log(data);
            })
            .always(function (data) {
                setTimeout(() => {
                    // enable btn
                    btn.removeClass("disabled").prop("disabled", false);
                    if (loader == "flash-icon") {
                        btn.html(btnHTML);
                    } else {
                        btn.find(".spinner").remove();
                    }
                }, 500);
            });
    });

    function updateCompareInfo(compare) {
        $(".compare_count").text(compare.quantity);
    }
    /* end compare */

    /* input number change */
    $('[data-toggle="increment"], [data-toggle="decrement"]').on("click", function () {
        let changeValue = $(this).data('toggle') == 'decrement' ? -1 : 1;
        let input = $(this).parent().find('input');
        let currentValue = +input.val();
        let max = +input.attr('max');
        let newValue = +input.val() + changeValue;
        if (newValue < 1) {
            newValue = 1;
        } else if (newValue > max) {
            newValue = max;
        }
        if (newValue != currentValue) {
            input.val(newValue);
            input.trigger("change");
        }
    });

    /* choose a region */
    $(".regions-list-group .list-group-item").on("click", function (e) {
        e.preventDefault();
        let btn = $(this);
        let regionID = btn.data("region-id");
        let form = btn.closest("form");
        btn.addClass("disabled").prop("disabled", true).append(spinnerHTML());
        form.find('[name="region_id"]').val(regionID);
        form.trigger("submit");
    });
    $(".confirm-default-region-btn").on("click", function (e) {
        e.preventDefault();
        let btn = $(this);
        let regionID = btn.data("region-id");
        let form = $(".regions-list-form");
        btn.addClass("disabled").prop("disabled", true).html(spinnerHTML());
        form.find('[name="region_id"]').val(regionID);
        form.trigger("submit");
    });
    $(".regions-list-form").on("submit", function (e) {
        e.preventDefault();
        let form = $(this);
        let sendUrl = form.attr("action");
        let sendData = form.serialize();
        let message = "";
        $.ajax({
            url: sendUrl,
            method: "post",
            dataType: "json",
            data: sendData,
            beforeSend: function () {
                form.find(".form-result").empty();
            },
        })
            .done(function (data) {
                message = `<div class="alert alert-success">
                            ${data.message}
                            </div>`;
                form.find(".form-result").html(message);
                $(".regions-list-group").hide();
                setTimeout(function () {
                    location.reload();
                }, 500);
            })
            .fail(function (data) {
                // console.log(data);
                if (data.status == 422) {
                    let result = data.responseJSON;
                    let messageContent = result.message + "<br>";
                    for (let i in result.errors) {
                        messageContent +=
                            "<span>" + result.errors[i] + "</span><br>";
                    }
                    message = `<div class="alert alert-danger">
                            ${messageContent}
                            </div>`;
                    form.find(".form-result").html(message);
                }
            })
            .always(function () {
                // enable button
                form.find(".list-group-item")
                    .removeClass("disabled")
                    .prop("disabled", false)
                    .find(".spinner")
                    .remove();
            });
    });

    let acceptCookie = localStorage.getItem("accept_cookie");
    if (!acceptCookie) {
        $(".accept-cookie").addClass("active");
    }
    $(".accept-cookie-btn").on("click", function (e) {
        e.preventDefault();
        localStorage.setItem("accept_cookie", "1");
        $(".accept-cookie").removeClass("active");
    });
    /* end theme scripts */


    // analytics
    $('.add-to-wishlist-btn-analytics').on('click', function(){
        gtag('event', 'click', {event_category: 'dobavit-v-izbrannoe'});
        ym(84743134,'reachGoal','dobavit-v-izbrannoe');
        return true;
    });
    $('.add-to-cart-btn-analytics').on('click', function(){
        gtag('event', 'click', {event_category: 'knopka-kupit'});
        ym(84743134,'reachGoal','knopka-kupit');
        return true;
    });
    $('.place-order-btn').on('click', function(){
        gtag('event', 'click', {event_category: 'oformit-zakaz'});
        ym(84743134,'reachGoal','oformit-zakaz');
        return true;
    });

    // place order btn
    $('#checkout-form').on('submit', function(){
            // YANDEX METRICS
            var yandexEcompId = $('#yandex-ecom-product-id').val();
            var yandexEcomName = $('#yandex-ecom-product-name').val();
            var yandexEcompPrice = $('#yandex-ecom-product-price').val();
            var yandexEcomQuantity = $('#yandex-ecom-product-quantity').val();

            // GOOGLE ANALYTICS
            gtag('event', 'purchase', {
                affiliation: 'Google Store',
                currency: 'UZS',
                items: [{
                    item_id: yandexEcompId,
                    item_name: yandexEcomName,
                    coupon: yandexEcomName,
                    affiliation: 'Google Store',
                    price: yandexEcompPrice,
                    currency: 'UZS',
                    quantity: yandexEcomQuantity
            }],
                transaction_id: 'T_12345'
            });

            gtag('event', 'click', {
                event_category: 'oformit-zakaz'
            });
            // GOOGLE ENDS

            dataLayer.push({
                "ecommerce": {
                    "currencyCode": "UZS",
                    "purchase": {
                        "actionField": {
                            "id" : "TRX987"
                        },
                        "products": [
                            {
                                "id": yandexEcompId,
                                "name" : yandexEcomName,
                                "price": yandexEcompPrice,
                                "quantity" : yandexEcomQuantity
                            }
                        ]
                    }
                }
            });

            ym(84743134,'reachGoal','oformit-zakaz'); return true
    });


    $('.catalog-menu-d-nav__list-switch').on('click', function(){
        $(this).closest('.catalog-menu-d-nav__list').find('li').css('display', '');
        $(this).hide();
    });

    setInterval(() => {
        $('.bestseller_card').delay(2000).fadeOut(1000).fadeIn(1000);
    }, 1000);

}); // ready end

// resize
$(window).on("resize", function () {
    //
});

// load
$(window).on("load", function () {
    //
});

function isScrolledIntoView(elem) {
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();

    return elemBottom <= docViewBottom && elemTop >= docViewTop;
}

function refreshCaptcha(obj) {
    $(".captcha-container img").each(function () {
        let img = $(this);
        let container = img.closest(".captcha-container");
        // console.log(container);
        $.ajax({
            url: "/captcha/api/flat",
        }).done(function (data) {
            img.attr("src", data.img);
            container.find('[name="captcha_key"]').remove();
            container.append(
                '<input type="hidden" name="captcha_key" value="' +
                    data.key +
                    '">'
            );
        });
    });
}

function spinnerHTML() {
    return `<span class="spinner mx-1"><svg class="svg-spinner" width="24" height="24" viewBox="0 0 50 50"><circle class="svg-spinner-path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg></span>`;
}

$(".buy-in-one-click").each(function() {
    $(this).on('click', function(){
        var modalImage = $(this).attr('data-image');
        var modalName = $(this).attr('data-name');
        var modalPrice = $(this).attr('data-price');

        $('.input-name-show').attr('value', modalName);
        $('.modal-image-show').attr('src', modalImage);
        $('.modal-name-show').text(modalName);
        $('.modal-price-show').html(modalPrice);
    });
});
$("#startConfetti").on("click", function() {
	$(window).scrollTop(0);
});

$('#mysearchone').on('keyup', function(){
    var value = $(this).val();
    value = value.toLowerCase();

    let headerD = $('.header-d');
    let headerM = $('.header-m');

    $('.search-history-show').addClass('d-none');

    if (value.includes("айфон")) {
        value = value.replace('айфон', 'iphone');
    }

    function transliterate(word){
        var answer = ""
        , a = {};

        a["Ё"]="YO";a["Й"]="I";a["Ц"]="TS";a["У"]="U";a["К"]="K";a["Е"]="E";a["Н"]="N";a["Г"]="G";a["Ш"]="SH";a["Щ"]="SCH";a["З"]="Z";a["Х"]="H";a["Ъ"]="'";
        a["ё"]="yo";a["й"]="i";a["ц"]="ts";a["у"]="u";a["к"]="k";a["е"]="e";a["н"]="n";a["г"]="g";a["ш"]="sh";a["щ"]="sch";a["з"]="z";a["х"]="h";a["ъ"]="'";
        a["Ф"]="F";a["Ы"]="I";a["В"]="V";a["А"]="a";a["П"]="P";a["Р"]="R";a["О"]="O";a["Л"]="L";a["Д"]="D";a["Ж"]="ZH";a["Э"]="E";
        a["ф"]="f";a["ы"]="i";a["в"]="v";a["а"]="a";a["п"]="p";a["р"]="r";a["о"]="o";a["л"]="l";a["д"]="d";a["ж"]="zh";a["э"]="e";
        a["Я"]="Ya";a["Ч"]="CH";a["С"]="S";a["М"]="M";a["И"]="I";a["Т"]="T";a["Ь"]="'";a["Б"]="B";a["Ю"]="YU";
        a["я"]="ya";a["ч"]="ch";a["с"]="s";a["м"]="m";a["и"]="i";a["т"]="t";a["ь"]="'";a["б"]="b";a["ю"]="yu";

        for (i in word){
            if (word.hasOwnProperty(i)) {
                if (a[word[i]] === undefined){
                    answer += word[i];
                } else {
                    answer += a[word[i]];
                }
            }
        }
        value = answer;
        return value;
    }

    transliterate(value);

    console.log('____'+value+'_____');
    $.ajax({
        type: "get",
        url: "/searchable",
        data: {'search':value},
        success: function (data) {
            console.log(data);
            headerD.addClass('fixed');
            headerM.addClass('fixed');
            $('.ajax-search-results').addClass('d-block');
            $('.searchBody').html(data);
        }
    }).fail(function(data) {
        // console.log(data);
        results.find('.list-group').empty();
        results.removeClass('active');

        headerD.removeClass('fixed');
        headerM.removeClass('fixed');
    }).always(function(){
        setTimeout(() => {
            $('.ajax-search-results').addClass('d-none');
        }, 200);
    });
});

$('body').on('click', function(e){
    $('.ajax-search-results').addClass('d-none');
    hideAjaxSearch();
});
function hideAjaxSearch() {
    let headerD = $('.header-d');
    let headerM = $('.header-m');

    $('.ajax-search-results').removeClass('active');
    headerD.removeClass('fixed');
    headerM.removeClass('fixed');
}

$(".search-btn").on("click", function() {

    var search = $('.input-field-search').val();

    console.log('____'+search+'_____');
    $.ajax({
        type: "post",
        url: "/addmysearch",
        data: {'search':search},
        success: function (data) {
            console.log(data);
        }
    }).fail(function(data) {
        console.log("failed");
    });
});

$(".bottom_contact_form_click").on('click', function(){
    $(".bottom_contact_form").toggleClass("d-none", "d-block");
});

// the alert panel should be hidden.
        if (getCookie('accepted') !== null && getCookie('accepted') === 'yes') {
            document.getElementById("alert").style.display = "none";
        }

        // user clicks the confirmation -> set the 'yes' value to cookie and set 'accepted' as name
        function accpetCookie() {
            setCookie('accepted', 'yes', 100);
        }
