$(function() {

    // Helper for Facebook Post Type changed showing other fields
    $(document).on('change', '#providerSettings-facebook-endpoint', function(e) {
        var val = $(this).val();

        $('#providerSettings-facebook-groupId-field').hide();
        $('#providerSettings-facebook-pageId-field').hide();

        if (val == 'group') {
            $('#providerSettings-facebook-groupId-field').show();
        }

        if (val == 'page') {
            $('#providerSettings-facebook-pageId-field').show();
        }
    });

    $('#providerSettings-facebook-endpoint').trigger('change');


});
