$(document).ready(function() {
	$(".modal_on").fancybox();
	//
	$("a[rel=photo_group], a[rel=my_photo]").fancybox({
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
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
	$('#forward')	.on('click', function() {
		$('.registration-form fieldset[id]').hide();
		$('#step2').fadeIn(700);
		$('.registration-breadcrumb a').removeClass('active');
		$('.registration-breadcrumb a[data-id="step2"]').addClass('active');
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
	//
	$('#masters-select').on('change', function(){
		var act = $('#masters-select option:selected').val();
		$(this).parents('form.search-block-form').attr("action", "/"+act+"/");
                if(act === 'companies') $(this).parents('form.search-block-form').attr("action", "/masters/");
		if ( act == 'masters' || act === 'companies' ) {
			$('#jobs-select').attr('name', "areas_for_user[]");
		} else if ( act == 'objects' ) {
			$('#jobs-select').attr('name', "areas_for_object[]");
		} else if ( act == 'jobs' ) {
			$('#jobs-select').attr('name', "areas_for_job[]");
		}
	});
	//
	$('#facetype').on('change', function() {
		var ftype = $('#facetype option:selected').val();
		if ( ftype == '1' ) {
			$('.fiz-facetype').show();
			$('.ur-facetype').hide();
		} else if ( ftype == '2' ) {
			$('.fiz-facetype').hide();
			$('.ur-facetype').show();
		}
	});
	//
	function handleFileSelect(evt) {
	    var files = evt.target.files; // FileList object
	    // Loop through the FileList and render image files as thumbnails.
	    for (var i = 0, f; f = files[i]; i++) {
			// Only process image files.
			if (!f.type.match('image.*')) {continue;}
			var reader = new FileReader();
			// Closure to capture the file information.
			reader.onload = (function(theFile) {
				return function(e) {
				// Render thumbnail.
					var span = document.createElement('span');
					span.innerHTML = ['<img class="thumb" src="', e.target.result,
					                '" title="', escape(theFile.name), '"/>'].join('');
					document.getElementById('ava-photo').insertBefore(span, null);
					document.getElementById('add_photo_save').style.display="block";
				};
			})(f);
			// Read in the image file as a data URL.
			reader.readAsDataURL(f);
	    }
	}
        if(document.getElementById('ava-files') !== null) document.getElementById('ava-files').addEventListener('change', handleFileSelect, false);
	//
	$('#add-pricerow').on('click', function(){
            var rowHTML = '<div class="add-price-table-row clearfix">'+
                    '<div class="add-price-name">'+
                        '<input type="text" name="name[]">'+
                    '</div>'+
                    '<div class="add-price-price">'+
                        '<input type="text" name="amount[]">'+
                    '</div>'+
                    '<div class="add-price-value">'+
                        '<input type="text" name="value[]">'+
                    '</div>'+
                '</div>';
            $('.add-price-table').append(rowHTML);
            return false;
	});
	//
	var startftype = $('#facetype option:selected').val();
	if ( startftype == '1' ) {
		$('.fiz-facetype').show();
		$('.ur-facetype').hide();
	} else if ( startftype == '2' ) {
		$('.fiz-facetype').hide();
		$('.ur-facetype').show();
	}
	//
	function handleFileSelectName(evt) {
		var files = evt.target.files; // FileList object
		// files is a FileList of File objects. List some properties.
		var output = [];
		for (var i = 0, f; f = files[i]; i++) {
		  output.push('<li>', escape(f.name), '</li>');
		}
		document.getElementById('names-list').innerHTML = '<ul>' + output.join('') + '</ul>';
	}
	if(document.getElementById('name-files') !== null) document.getElementById('name-files').addEventListener('change', handleFileSelectName, false);
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