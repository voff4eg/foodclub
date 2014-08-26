$(document).ready(function() {
	onDocumentReady();
});

function userpicLayer($elem) {
	if(!$elem) {
		var $elem = $("body");
	}
	
	$elem.find(".b-userpic").each(function() {
		var $this = $(this);
		
		var $layer = $('<span class="b-userpic__layer"></span>');
		$this.prepend($layer);
		
		var cls = "b-userpic__layer__" + $this.find("img").attr("width");
		$layer.addClass(cls);
	});
}

function onDocumentReady() {
	userpicLayer();
}