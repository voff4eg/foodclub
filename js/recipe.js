(function ($) {
	"use strict";
	
	$(function () {
		$( ".stage .photo" ).each( function () {
			var $this = $( this );			
			if ( !$this.data( "src" ) ) $this.addClass( "i-no-progress" );
		});
	});
	
	window.onload = function () {
		$( ".stage .photo" ).each( function () {
			var $this = $( this ),
				src = $this.data( "src" ),
				img;
			
			if ( !src ) return;
			
			img = new Image();
			img.src = src;
			$( img ).load( function () {
				$this
					.attr( { src: src })
					.addClass( "i-no-progress" );
				img = null;
			});
		});
	};
	
}( jQuery ));