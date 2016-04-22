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
			subUlHeight = $this.find('.searcher-sub-categories').height() + 22;
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
});