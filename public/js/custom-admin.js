$(function(){
    $('body').on('click', '.remove-block', function(e){
        e.preventDefault();
        $(this).closest('.removable-block').remove();
    });

    $('.datepicker-date-only').each(function(){
        let minDate;
        if ($(this).data('min-date')) {
            minDate = $(this).data('min-date');
        }
        let dateOptions = {
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
             },
            format: 'YYYY-MM-DD'
        };
        if (minDate) {
            dateOptions.minDate = minDate;
        }
        $(this).datetimepicker(dateOptions);
    });

    $('.datetimepicker-from-now').each(function(){
        let minDate;
        if ($(this).data('min-date')) {
            minDate = $(this).data('min-date');
        }
        let dateOptions = {
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
             },
            format: 'YYYY-MM-DD HH:mm'
        };
        if (minDate) {
            dateOptions.minDate = minDate;
        }
        $(this).datetimepicker(dateOptions);
    });

    $('.change-status-btn').on('click', function(e) {
        e.preventDefault();

        let btn = $(this);

        if (btn.hasClass('disabled')) {
            return false;
        }

        let currentContainer = btn.closest('.current-status-container');
        let currentStatusText = currentContainer.find('.current-status-text');
        let url = btn.data('target');
        let text = btn.data('text');

        $.ajax({
            url: url,
            beforeSend: function(){
                btn.addClass('animated infinite flash disabled');
            }
        })
            .done(function(data){
                // console.log(data);
                currentStatusText.text(text);
                currentContainer.find('.btn').removeClass('disabled');
                btn.addClass('disabled')
            })
            .fail(function(data){
                console.log(data);
            })
            .always(function(data){
                btn.removeClass('animated infinite flash');
            });
    });

});

function getRandomString(length = 32) {
    let str = '';
    let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (let i = 0; i < length; i++) {
        let char = Math.floor(Math.random() * chars.length + 1);
        str += chars.charAt(char)
    }
    return str;
}

function initSelects()
{
    $('select.select2').select2({width: '100%'});
}

// tiny mce
function tinymce_init_callback(editor)
{
    editor.remove();
    editor = null;
    tinymce.init({
        menubar: 'file edit view insert format tools table tc help',
        selector:'textarea.richTextBox',
        skin_url: $('meta[name="assets-path"]').attr('content')+'?path=js/skins/voyager',
        min_height: 600,
        resize: 'vertical',
        plugins: 'link, image, code, table, textcolor, lists',
        extended_valid_elements : 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
        file_browser_callback: function(field_name, url, type, win) {
            if(type =='image'){
                $('#upload_file').trigger('click');
            }
        },
        toolbar: 'undo redo | removeformat | styleselect bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor  | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image table media template link anchor codesample | ltr rtl | code',


        //toolbar: 'styleselect bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | link image table | code',

        convert_urls: false,
        image_caption: true,
        image_title: true
    });
}
