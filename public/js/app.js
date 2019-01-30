$('#add-media').on('click', function () {
    var count = $('#trick_media div.form-group').length;
    var tmpl = $('#trick_media').data('prototype').replace(/__name__/g, count);
    $('#trick_media').append(tmpl);
});

$('div').on('click', '.delete-button', function() {
    $(this).parent().parent().remove();
});