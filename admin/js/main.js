jQuery(document).ready(function ($) {
    const $selectors = {
        userFiles: 'js-ufru-user-files',
        toggleBtn: 'js-ufru-btn-toggle',
        toggleBtnShow: 'js-ufru-btn-toggle-show',
        toggleBtnHide: 'js-ufru-btn-toggle-hide',
    }

    const $hooks = {
        userFiles: `[${$selectors.userFiles}]`,
        toggleBtn: `[${$selectors.toggleBtn}]`,
        toggleBtnShow: `[${$selectors.toggleBtnShow}]`,
        toggleBtnHide: `[${$selectors.toggleBtnHide}]`,
    };

    const $dom = {
        toggleBtn: $($hooks.toggleBtn),
    };

    $dom.toggleBtn.on('click', (e) => {
        const showMoreText = $(e.currentTarget).find($hooks.toggleBtnShow);
        const showLessText = $(e.currentTarget).find($hooks.toggleBtnHide);
        const userFilesWrapper = $(e.currentTarget).siblings($hooks.userFiles);

        if (e.currentTarget.getAttribute($selectors.toggleBtn) === "0") {
            // Show items
            e.currentTarget.setAttribute($selectors.toggleBtn, 1);
            showMoreText.removeClass('show');
            showMoreText.addClass('hide');
            showLessText.removeClass('hide');
            showLessText.addClass('show');

            userFilesWrapper.addClass('expand-files');
        } else {
            // Hide Items
            e.currentTarget.setAttribute($selectors.toggleBtn, 0);
            showMoreText.removeClass('hide');
            showMoreText.addClass('show');
            showLessText.removeClass('show');
            showLessText.addClass('hide');

            userFilesWrapper.removeClass('expand-files');
        }
    })
});
