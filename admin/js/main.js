jQuery(document).ready(function ($) {
    const selectors = {
        userFiles: 'js-ufru-user-files',
        userFilesWrapper: 'js-ufru-user-files-wrapper',
        toggleBtn: 'js-ufru-btn-toggle',
        toggleBtnStatus: 'js-ufru-user-files-status',
        toggleBtnShow: 'js-ufru-btn-toggle-show',
        toggleBtnHide: 'js-ufru-btn-toggle-hide',
    }

    const hooks = {
        userFiles: `[${selectors.userFiles}]`,
        userFilesWrapper: `[${selectors.userFilesWrapper}]`,
        toggleBtn: `[${selectors.toggleBtn}]`,
        toggleBtnStatus: `${selectors.toggleBtnStatus}`,
        toggleBtnShow: `[${selectors.toggleBtnShow}]`,
        toggleBtnHide: `[${selectors.toggleBtnHide}]`,
    };

    const $dom = {
        toggleBtn: $(hooks.toggleBtn),
        userFilesWrappers: $(hooks.userFiles)
    };

    $dom.toggleBtn.on('click', (e) => {
        console.log('click', $(e.currentTarget));
        const showMoreText = $(e.currentTarget).parents('tr').find(hooks.toggleBtnShow);
        const showLessText = $(e.currentTarget).parents('tr').find(hooks.toggleBtnHide);
        const userFilesWrapper = $(e.currentTarget).parents('tr').find(hooks.userFilesWrapper);
        console.log('more: ', showMoreText);
        if (userFilesWrapper.attr(selectors.toggleBtnStatus) === "0") {
            // Show items
            userFilesWrapper.attr(selectors.toggleBtnStatus, 1);
            showMoreText.removeClass('show');
            showMoreText.addClass('hide');
            showLessText.removeClass('hide');
            showLessText.addClass('show');

            userFilesWrapper.addClass('expand-files');
        } else {
            // Hide Items
            userFilesWrapper.attr(selectors.toggleBtnStatus, 0);
            showMoreText.removeClass('hide');
            showMoreText.addClass('show');
            showLessText.removeClass('show');
            showLessText.addClass('hide');

            userFilesWrapper.removeClass('expand-files');
        }
    })

    $dom.userFilesWrappers.each((i, el) => {
        const resize_ob = new ResizeObserver(function(entries) {
            // since we are observing only a single element, so we access the first element in entries array
            const rect = entries[0].contentRect;
            const height = rect.height;
            const imgHeight = $(entries[0].target).find('img').height();
        
            $(entries[0].target).css({
                "max-height": `${imgHeight}px`,
            })
        });
        resize_ob.observe(el);
    });
});
