(function ($) {
    // skip plugin installation confirmation prompt
    $('a.install-now').off('click');

    // set focus to search field
    $('.wp-filter .search-form input[type="search"]').focus();
}(jQuery));
