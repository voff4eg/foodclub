$(document).ready(function(){
	$(document).click(function() {
		$("#table table").find("td").removeClass("click");
	});
	$("#table table").find("td").hover(function() {
		var classN = new String(this.className.slice(4, 8));
		$("#table table").find("td." + classN).addClass("hover");
	},function() {
		$("#table table").find("td").removeClass("hover");
	});
	$("#table table").find("td").click(function(e) {
		$("#table table").find("td").removeClass("click");
		var classN = new String(this.className.slice(4, 8));
		$("#table table").find("td." + classN).addClass("click");
		stopEvent(e);
	});
});

function stopEvent(e) {
	if(!e) e = window.event;
	if(e.stopPropagation) e.stopPropagation();
	else e.cancelBubble = true;
}
