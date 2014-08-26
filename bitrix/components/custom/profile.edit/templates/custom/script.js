/*
 * jQuery File Upload Plugin JS Example 7.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(function () {
    'use strict';

    $('#fileupload').fileupload({
        url: '/js/file-upload/server/php/avatar.php'
    });
	
	$(".b-file-upload__type_avatar").hover(function() {
		$(this).addClass("i-hover");
	}, function() {
		$(this).removeClass("i-hover");
	});
});