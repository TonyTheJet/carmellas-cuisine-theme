$(document).ready(function(){

    var meal_order = new MealOrder($('#order-meal-app'));
});


function MealOrder(app_root){
    this.app_root = app_root;

    // event handlers
    this.attach_event_handlers();
    // end event handlers
}


MealOrder.prototype.attach_event_handlers = function(){
    this.attach_pickup_date_event_handlers();
    this.attach_step_event_handlers();
};

MealOrder.prototype.attach_pickup_date_event_handlers = function(){
    var this_ref = this;

    $('.pick-up-date').on('click', function(e){
        e.stopPropagation();

        if ($(this).hasClass('selected')){
            $(this).removeClass('selected');
        } else {
            $(this).addClass('selected');
            var this_id = $(this).data('date_id');
            $('.pick-up-date').each(function(){
                if ($(this).data('date_id') !== this_id){
                    $(this).removeClass('selected');
                }
            });
        }

        this_ref.step_1_refresh_validation();
    });
};

MealOrder.prototype.attach_step_event_handlers = function(){
    var this_ref = this;
    $('.load-step').on('click', function(e){
        e.stopPropagation();

        if (!$(this).is(':disabled')){
            this_ref.jump_to_step($(this).data('load_step'));
        }
    });



};

/**
 *
 * @param {number} step
 */
MealOrder.prototype.jump_to_step = function(step){

    // set the header
    $('.order-meal-step').removeClass('active');
    $('#order-meal-step-' + step).addClass('active');

    // show the body
    $('.order-meal-step-body').addClass('hidden');
    $('#order-meal-step-' + step + '-body').removeClass('hidden');
};


MealOrder.prototype.step_1_refresh_validation = function(){
    var has_selection = false;

    $('.pick-up-date').each(function(){
        if ($(this).hasClass('selected')){
            has_selection = true;
            return true;
        }
    });

    if (has_selection){
        $('#step-1-continue-btn').removeAttr('disabled');
    } else {
        $('#step-1-continue-btn').attr('disabled', 'disabled');
    }
};