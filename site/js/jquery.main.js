$(document).ready(function() {
	$(".modal_on").fancybox();
	//
	var Hheader = $('.header').height(),
		Hwrap = $(window).height()-Hheader+6;
	$('.wrapper').css('min-height', Hwrap-Hheader);
	//
	var Hleft = $('.column-left').height();
	$('.column-searcher-holder').css('min-height', Hleft-30);
	//
	var Hright = $('.my-page-wrapper').height();
	$('.my-page-navbar').css('height', Hright-10);
	//
	$('.searcher-categories li').on('click', function(){
		var $this = $( this ),
			subUlHeight = $this.find('.searcher-sub-categories').height() + 20;
		if ( $this.hasClass('active') ) {
			$this.css('height', '20px').removeClass('active');
		}
		else {
			$this.css('height', subUlHeight).addClass('active');
		}
		return false;
	});
	//
	$(function() {
        $( "#from" ).datepicker({
          defaultDate: "+1w",
          changeMonth: false,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
          }
        });
        $( "#to" ).datepicker({
          defaultDate: "+1w",
          changeMonth: false,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( "#from" ).datepicker( "option", "maxDate", selectedDate );
          }
        });
    });
    //
    $('.star-master').on('click', function(){
    	var $this = $( this );
    	if ( $this.hasClass('active') ) {$this.removeClass('active');}
    	else {$this.addClass('active');}
    });
    //
    $('.registration-breadcrumb a').on('click', function() {
		var curid = $(this).attr('data-id');
		$('.registration-breadcrumb a').removeClass('active');
		$(this).addClass('active');
		$('.registration-form fieldset[id]').hide();
		$('#'+ curid).fadeIn(700);
		return false;
	});	
	//
	$('.searcher-sub-categories li .searcher-categories-item label').on('click', function() {
		var $this = $( this );
		var attr = $(this).find('input').attr('checked');
		
		if ( attr == 'checked' ) {
			$(this).find('input').attr('checked', 'checked');
		}
		else {
			$(this).find('input').attr('checked', '');
		}

	});
});
//
jQuery(document).ready(function ($) {
	var jssor_1_options = {
		$AutoPlay: true,
		$AutoPlaySteps: 4,
		$SlideDuration: 160,
		$SlideWidth: 200,
		$SlideSpacing: 3,
		$Cols: 4,
		$ArrowNavigatorOptions: {
			$Class: $JssorArrowNavigator$,
			$Steps: 4
		},
		$BulletNavigatorOptions: {
			$Class: $JssorBulletNavigator$,
			$SpacingX: 1,
			$SpacingY: 1
		}
	};
	var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);
	//responsive code begin
	//you can remove responsive code if you don't want the slider scales while window resizing
	function ScaleSlider() {
	    var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
	    if (refSize) {
	        refSize = Math.min(refSize, 809);
	        jssor_1_slider.$ScaleWidth(refSize);
	    }
	    else {
	        window.setTimeout(ScaleSlider, 30);
	    }
	}
	ScaleSlider();
	$(window).bind("load", ScaleSlider);
	$(window).bind("resize", ScaleSlider);
	$(window).bind("orientationchange", ScaleSlider);
	//responsive code end
});