var HOME_POPUP_CLOSE_CLASS = 'home-popup-close';
var HOME_POPUP_WRAPPER_ID = 'home-pop-up';
var SCREEN_OVERLAY_CLASS = 'semi-opaque-overlay';

$(document).ready(function(){
	
	//clicking the pop-up "close" button closes it
	$('.' + HOME_POPUP_CLOSE_CLASS).on('click', function(){
		home_popup_close();
	});
	
	//clicking inside the pop-up stops propagation
	$('#' + HOME_POPUP_WRAPPER_ID).on('click', function(e){
		e.stopPropagation();
	});
	
	//any click closes the pop-up
	$('html').on('click', function(e){
		home_popup_close();
	});
});

/**
* hides the overlay ad
* 
*/
function home_popup_close(){
	$('#' + HOME_POPUP_WRAPPER_ID).addClass('hidden');
	$('.' + SCREEN_OVERLAY_CLASS).addClass('hidden');
}