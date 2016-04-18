"use strict";
$(document).ready(function() {
	var Hscreen = $(window).height(),
	    Hdocument = $(document).height();
	if (Hscreen<Hdocument) {$('.wrapper').css('padding-bottom', '154px');}
	//
	var Hleft = $('.column-left').height();
	$('.column-searcher-holder').css('height', Hleft-30);
	//
	var Hright = $('.my-page-wrapper').height();
	$('.my-page-navbar').css('height', Hright-10);
	//
	$('.searcher-categories li').on('click', function(){
		var $this = $( this ),
			subUlHeight = $this.find('.searcher-sub-categories').height() + 22;
		if ( $this.hasClass('active') ) {
			$this.css('height', '20px').removeClass('active');
		}
		else {
			$this.css('height', subUlHeight).addClass('active');
		}
		return false;
	});
});