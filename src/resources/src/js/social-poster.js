// ==========================================================================

// Social Poster Plugin for Craft CMS
// Author: Verbb - https://verbb.io/

// ==========================================================================

if (typeof Craft.SocialPoster === typeof undefined) {
    Craft.SocialPoster = {};
}

(function($) {

$(document).on('click', '[data-refresh-settings]', function(e) {
    e.preventDefault();

    const $btn = $(this);
    const $container = $btn.parent().parent();
    const $select = $container.find('select');
    const account = $btn.data('account');
    const setting = $btn.data('refresh-settings');

    const data = {
        account: account,
        setting: setting,
    }

    const setError = function(text) {
        let $error = $container.find('.sp-error');

        if (!text) {
            $error.remove();
        }

        if (!$error.length) {
            $error = $('<div class="sp-error error"></div>').appendTo($container);
        }

        $error.html(text);
    }

    const setSelect = function(values) {
        let currentValue = $select.val();
        let options = '';

        $.each(values, (key, option) => {
            options += '<option value="' + option.value + '">' + option.label + '</option>';
        });

        $select.html(options);

        // Set any original value back
        if (currentValue) {
            $select.val(currentValue);
        }
    }

    $btn.addClass('sp-loading sp-loading-sm');

    setError(null);

    Craft.sendActionRequest('POST', 'social-poster/accounts/refresh-settings', { data })
        .then((response) => {
            if (response.data.error) {
                let errorMessage = Craft.t('social-poster', 'An error occurred.');

                if (response.data.error) {
                    errorMessage += `<br><code>${response.data.error}</code>`;
                }

                setError(errorMessage)

                return;
            }

            setSelect(response.data);
        })
        .catch((error) => {
            let errorMessage = error;

            if (error.response && error.response.data && error.response.data.error) {
                errorMessage += `<br><code>${error.response.data.error}</code>`;
            }

            setError(errorMessage);
        })
        .finally(() => {
            $btn.removeClass('sp-loading sp-loading-sm');
        });
})



})(jQuery);
