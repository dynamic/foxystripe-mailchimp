(function ($) {
    $.entwine(function ($) {
        /**
         * Class: .cms-edit-form .field.switchable
         *
         * Say hello
         */
        var button = $('#Form_EditForm_action_updateMailing');
        button.entwine({
            onclick: function () {
                button.addClass('font-icon-spinner');
                console.log('loading...');
                $.get('./admin/settings/updateMailing').done(function () {
                    button.removeClass('font-icon-spinner');
                    button.addClass('font-icon-sync');
                }).fail(function () {
                    button.removeClass('font-icon-spinner');
                    button.addClass('font-icon-sync');
                });
            }
        });
    });
})(jQuery);
