$(document).ready(function(){
    $('#secondary-menu-toggle-wrapper').on('click', function(){
        toggle_secondary_menu();
    });
});


function toggle_secondary_menu(){
    let secondary_menu_wrapper_el = $('#secondary-menu-wrapper');
    if (secondary_menu_wrapper_el.hasClass('hidden-xs')){
        secondary_menu_wrapper_el.removeClass('hidden-xs');
    } else {
        secondary_menu_wrapper_el.addClass('hidden-xs');
    }
}