$(document).ready(function(){

    var meal_order = new MealOrder($('#order-meal-app'));
});


function MealOrder(app_root){

    // constants
    this.TAX_RATE = 0.081;
    // end constants

    this.app_root = app_root;
    this.meal_items_el = $('#meal-items');

    /**
     *
     * @type {MealOrderItem[]}
     */
    this.order_items = [];
    this.order_sales_tax = 0.00;
    this.order_sales_tax_el = $('#order-sales_tax');
    this.order_subtotal = 0.00;
    this.order_subtotal_el = $('#order-subtotal');
    this.order_total = 0.00;
    this.order_total_el = $('#order-total');
    this.pickup_date_id = null;
    this.step_1_continue_btn = $('#step-1-continue-btn');
    this.step_2_continue_btn = $('#step-2-continue-btn');
    this.step_3_continue_btn = $('#step-3-continue-btn');
    this.step_4_continue_btn = $('#step-4-continue-btn');

    // event handlers
    this.attach_event_handlers();
    // end event handlers
}


MealOrder.prototype.attach_event_handlers = function(){
    this.attach_pickup_date_event_handlers();
    this.attach_step_event_handlers();
};

MealOrder.prototype.attach_meal_item_handlers = function(){
    var this_ref = this;
    this.meal_items_el.find('.meal-item').each(function(){
        var qty_el = $(this).find('.quantity');
        qty_el.on('change keyup mouseup', function(){
            this_ref.refresh_order_items();
        });
    });
};

MealOrder.prototype.attach_pickup_date_event_handlers = function(){
    var this_ref = this;

    $('.pick-up-date').on('click', function(e){
        e.stopPropagation();

        if ($(this).hasClass('selected')){
            $(this).removeClass('selected');
            this_ref.pickup_date_id = null;
        } else {
            $(this).addClass('selected');
            this_ref.pickup_date_id = $(this).data('date_id');
            $('.pick-up-date').each(function(){
                if ($(this).data('date_id') !== this_ref.pickup_date_id){
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

    // call the step's "load" function
    this['load_step_' + step]();
};


MealOrder.prototype.load_step_1 = function(){
    console.log('loading step 1');
    this.step_2_show_loading_gif();
};

MealOrder.prototype.load_step_2 = function(){
    if (!this.meal_items_el.find('.meal-item').length){
        this.step_2_show_loading_gif();
        this.order_items = [];
        var this_ref = this;
        $.post(
            '/',
            {
                'action': 'fetch_items_for_pickup_date',
                'post_id': this.pickup_date_id
            },
            /**
             *
             * @param response
             * @param {string} response.message
             * @param {Object} response.pickup_date
             * @param {string} response.pickup_date_date_string
             * @param {Array} response.pickup_date_items
             * @param {Object} response.pickup_date_items.basic_data
             * @param {number} response.pickup_date_items.basic_data.ID;
             * @param {string} response.pickup_date_items.basic_data.post_title
             * @param {number} response.pickup_date_items.bulk_price
             * @param {number} response.pickup_date_items.minimum_bulk_price_quantity
             * @param {number} response.pickup_date_items.price
             * @param {string} response.pickup_date_items.thumbnail_url
             */
            function(response){
                if (response.success){
                    var html_string = '';
                    for (var i = 0; i < response.pickup_date_items.length; i++){
                        html_string += '<div class="meal-item col-xs-12 col-sm-6 col-md-4 col-lg-3" data-menu_item_id="' + response.pickup_date_items[i].basic_data.ID + '" data-bulk_price="' + response.pickup_date_items[i].bulk_price +'" data-minimum_bulk_price_quantity="' + response.pickup_date_items[i].minimum_bulk_price_quantity + '" data-price="' + response.pickup_date_items[i].price + '">';
                        html_string +=      '<h3>' + response.pickup_date_items[i].basic_data.post_title + '</h3>';
                        html_string +=          '<div class="meal-item-img-wrapper" style="background-image: url(\'' +  response.pickup_date_items[i].thumbnail_url + '\')"></div>';
                        html_string +=          '<div class="price">Price each: <strong>$' + response.pickup_date_items[i].price + '</strong></div>';
                        html_string +=          '<div class="price">Group price (qty ' + response.pickup_date_items[i].minimum_bulk_price_quantity + ' or more): <strong>$' + response.pickup_date_items[i].bulk_price + '</strong></div>';
                        html_string +=          '<div class="quantity-wrapper"><strong>Quantity: </strong><input type="number" class="quantity" min="0" max="9999" step="1" value="0" /></div>';
                        html_string += '</div>';
                    }
                    this_ref.meal_items_el.html(html_string);
                    this_ref.attach_meal_item_handlers();
                    this_ref.refresh_order_items();
                } else {
                    this_ref.meal_items_el.html('<h2 class="text-danger">' + response.message + '</h2>');
                }
            },
            'json'
        );
    }

};

MealOrder.prototype.load_step_3 = function(){
    console.log('loading step 3');
};

MealOrder.prototype.load_step_4 = function(){
    console.log('loading step 4');
};

MealOrder.prototype.refresh_order_items = function(){
    var this_ref = this;
    this.order_items = [];
    this.meal_items_el.find('.meal-item').each(function(){
        var this_qty = parseInt($(this).find('.quantity').val());
        if (this_qty > 0){
            var this_id = parseInt($(this).data('menu_item_id'));
            var this_bulk_price = parseFloat($(this).data('bulk_price'));
            var this_minimum_bulk_price_qty = parseInt($(this).data('minimum_bulk_price_quantity'));
            var price = parseFloat($(this).data('price'));
            if (this_qty >= this_minimum_bulk_price_qty){
                price = this_bulk_price;
            }
            this_ref.order_items.push(new MealOrderItem(this_id, price, this_qty));
        }
    });

    this.refresh_order_totals();
};

MealOrder.prototype.refresh_order_totals = function(){

    // calculate
    this.order_sales_tax = 0.00;
    this.order_subtotal = 0.00;
    this.order_total = 0.00;
    for (var i = 0; i < this.order_items.length; i++){
        this.order_subtotal += this.order_items[i].price * this.order_items[i].quantity;
    }
    this.order_subtotal = parseFloat(this.order_subtotal).toFixed(2);
    this.order_sales_tax = parseFloat(this.order_subtotal * this.TAX_RATE).toFixed(2);
    this.order_total = parseFloat(this.order_subtotal + this.order_sales_tax).toFixed(2);
    // end calculate

    // udpate elements
    this.order_subtotal_el.html(this.order_subtotal);
    this.order_sales_tax_el.html(this.order_sales_tax);
    this.order_total_el.html(this.order_total);
    // end update elements

    // refresh validation
    this.step_2_refresh_validation();
    // end refresh validation
};


MealOrder.prototype.step_1_refresh_validation = function(){
    if (this.pickup_date_id){
        this.step_1_continue_btn.removeAttr('disabled');
    } else {
        this.step_1_continue_btn.attr('disabled', 'disabled');
    }
};


MealOrder.prototype.step_2_refresh_validation = function(){
    if (this.order_items.length > 0){
        this.step_2_continue_btn.removeAttr('disabled');
    } else {
        this.step_2_continue_btn.attr('disabled', 'disabled');
    }
};

MealOrder.prototype.step_2_show_loading_gif = function(){
    this.meal_items_el.html('<img class="center-block" src="' + stylesheet_directory + '/images/eclipse-1s-200px.gif" />');
};


// order items
/**
 *
 * @param {number} id
 * @param {number} price
 * @param {number} quantity
 * @constructor
 */
function MealOrderItem(id, price, quantity){
    this.id = id;
    this.price = price;
    this.quantity = quantity;
}