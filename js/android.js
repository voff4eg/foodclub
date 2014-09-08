$(function() {
	if(navigator.userAgent.toLowerCase().search("android") == -1) return;
	$("html").addClass("i-android");
	
	$(".b-ab__close").click(function(e) {
		e.stopPropagation();
		$("#android-banner").slideUp(500, function() {
			$("body").removeClass("i-android");
		});
		$.cookie('android_banner', 'false', { expires: null, path: '/' });
		return false;
	});	
	$(".b-ab").bind("click", function() {
		ga('send', 'event', 'Android App banner', 'Переход на Google Play');
	});
	$(".b-ab__close").bind("click", function() {
		ga('send', 'event', 'Android App banner', 'Продолжить просмотр сайта');
	});
	
	if($.cookie('android_banner') && $.cookie('android_banner') == "false") return;
	
	$("html").addClass("i-android-banner");
});