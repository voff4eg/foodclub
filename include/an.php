<style>
#android-banner {
	display: none;
}
.i-android-banner #android-banner {
	display: block;
	text-align: center;
}
.i-android #top_panel {
	position: static;
}
.i-android #top_spacer {
	height: 0;
}
.b-ab__close {
	width: 8%;
	position: absolute;
	top: 15px;
	right: 15px;
}
html.i-android {
	overflow-y: none;
}
</style>

<script>
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
</script>

<div id="android-banner">
	<div class="i-relative"><a href="#" class="b-ab__close">
		<img src="/images/android/close.png" width="100%" alt="" />
	</a></div>
	<a href="https://play.google.com/store/apps/details?id=com.app.foodclub" target="_blank" class="b-ab"><img src="/images/android/android_small_banner.jpg" width="100%" alt=""></a>
</div>
