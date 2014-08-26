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
        url: '/js/file-upload/server/php/index.php',
		done: function(e, data) {
			$(".b-personal-page__intro__ill").removeClass("i-progress");
			var url = data.result.files[0].url;
			$(".b-personal-page__intro__ill").addClass("b-personal-page__intro__ill__type_custom").removeClass("i-hover");
			$(".b-personal-page__intro__ill__background, .b-personal-page__intro__ill__pic").css({backgroundImage: "url('" + url + "')"});
		}
    });
	
	$(".b-personal-page__intro__ill").hover(function() {
		$(this).addClass("i-hover");
	}, function() {
		$(this).removeClass("i-hover");
		$(".b-personal-page__intro__ill__error-message").empty();
	});
});
