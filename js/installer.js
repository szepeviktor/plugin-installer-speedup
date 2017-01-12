/*jslint
    browser
*/
/*global
    jQuery
*/
jQuery(function ($) {
    "use strict";

    // Skip plugin installation confirmation prompt
    $("a.install-now").off("click");

    // Set focus to search field
    $(".wp-filter .search-form input[type='search']").focus();
});
