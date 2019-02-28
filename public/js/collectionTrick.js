$(document).ready(function () {
    $('#add-video').on('click', function () {
        var count = $('#trick_videos div.form-group').length;
        var tmpl = $('#trick_videos').data('prototype').replace(/__name__/g, count);
        $('#trick_videos').append(tmpl);
    });

    $('#add-image').on('click', function () {
        var count = $('#trick_images div.form-group').length;
        var tmpl = $('#trick_images').data('prototype').replace(/__name__/g, count);
        $('#trick_images').append(tmpl);
    });

    $('div').on('click', '.delete-button', function() {
        $(this).parent().parent().remove();
    });
});

