'use strict';

/*
* ===========================================================
* DEFAULT SCRIPTS
* ===========================================================
* This file is loaded only if Hybrid Composer plugin is not active.
* 
* Pixor - Copyright (c) Federico Schiocchet - Pixor - Framework Y
*/

(function ($) {
    var menu_open = false;
    $(document).ready(function () {
        $("body").on("click", ".navbar-toggle", function () {
            if (menu_open) {
                $(".navbar-collapse").hide();
                menu_open = false;
            } else {
                $(".navbar-collapse").show();
                menu_open = true;
            }
        });
    });
}(jQuery));

