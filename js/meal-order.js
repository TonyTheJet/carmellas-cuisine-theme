$(document).ready(function(){

    var meal_order = new MealOrder($('#order-meal-app'));
});


function MealOrder(app_root){

    // constants
    this.TAX_RATE = 0.081;
    // end constants

    this.app_root = app_root;
    this.confirm_order_btn_el = $('#confirm-order');
    this.customer_email = null;
    this.customer_email_el = $('#customer-email');
    this.customer_name = null;
    this.customer_name_el = $('#customer-name');
    this.customer_notes = null;
    this.customer_notes_el = $('#customer-notes');
    this.customer_phone = null;
    this.customer_phone_el = $('#customer-phone');
    this.meal_items_el = $('#meal-items');

    this.order_id = null;
    /**
     *
     * @type {MealOrderItem[]}
     */
    this.order_items = [];
    this.order_sales_tax = 0.00;
    this.order_sales_tax_el = $('#order-sales_tax');
    this.order_subtotal = 0.00;
    this.order_subtotal_el = $('#order-subtotal');
    this.order_summary_table = $('#order-summary');
    this.order_summary_sales_tax = $('#order-summary-sales_tax');
    this.order_summary_subtotal = $('#order-summary-subtotal');
    this.order_summary_total = $('#order-summary-total');
    this.order_total = 0.00;
    this.order_total_el = $('#order-total');
    this.order_total_modal_el = $('#order-total-modal');
    this.pickup_date_string = null;
    this.pickup_date_id = null;
    this.step_1_continue_btn = $('.step-1-continue-btn');
    this.step_2_continue_btn = $('#step-2-continue-btn');
    this.step_3_continue_btn = $('#step-3-continue-btn');
    this.step_4_continue_btn = $('#step-4-continue-btn');

    // event handlers
    this.attach_event_handlers();
    // end event handlers
}


MealOrder.prototype.attach_confirm_order_handler = function(){
    var this_ref = this;
    this.confirm_order_btn_el.on('click', function(){
        this_ref.jump_to_step(4);
    });
};

MealOrder.prototype.attach_customer_info_event_handlers = function(){
    var this_ref = this;
    $('.customer-info').on('change', function(){
        this_ref.step_3_refresh_customer_info();
        this_ref.step_3_refresh_validation();
    });
};

MealOrder.prototype.attach_event_handlers = function(){
    this.attach_confirm_order_handler();
    this.attach_customer_info_event_handlers();
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
            this_ref.pickup_date_string = null;
        } else {
            $(this).addClass('selected');
            this_ref.pickup_date_id = $(this).data('date_id');
            this_ref.pickup_date_string = $(this).data('date_string');
            $('.pick-up-date').each(function(){
                if ($(this).data('date_id') !== this_ref.pickup_date_id){
                    $(this).removeClass('selected');
                }
            });
        }

        this_ref.step_1_refresh_validation();
        this_ref.jump_to_step(2);
    });
};

MealOrder.prototype.attach_step_event_handlers = function(){
    var this_ref = this;
    $('.load-step').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();

        if (!$(this).is(':disabled')){
            this_ref.jump_to_step($(this).data('load_step'));
        }
    });
};

/**
 *
 * @param {number} step
 * @uses MealOrder.prototype.load_step_1
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

/**
 */
MealOrder.prototype.load_step_1 = function(){
    this.step_2_show_loading_gif();
};

MealOrder.prototype.load_step_2 = function(){
    if (!this.meal_items_el.find('.meal-item').length){
        this.step_2_show_loading_gif();
        $('#choose-items-date').html(this.pickup_date_string);
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
                        html_string += '<div class="meal-item col-xs-12 col-sm-6 col-md-4 col-lg-3"  itemscope itemtype="http://schema.org/Product" data-menu_item_id="' + response.pickup_date_items[i].basic_data.ID + '" data-bulk_price="' + response.pickup_date_items[i].bulk_price +'" data-minimum_bulk_price_quantity="' + response.pickup_date_items[i].minimum_bulk_price_quantity + '" data-price="' + response.pickup_date_items[i].price + '">';
                        html_string +=      '<h3 itemprop="name" class="meal-item-name">' + response.pickup_date_items[i].basic_data.post_title + '</h3>';
                        html_string +=          '<div class="meal-item-img-wrapper" itemprop="image" style="background-image: url(\'' +  response.pickup_date_items[i].thumbnail_url + '\')"></div>';
                        html_string +=          '<div class="description" itemprop="description">' + response.pickup_date_items[i].basic_data.post_content + '</div>';
                        html_string +=          '<div class="price-wrapper">';
                        html_string +=              '<div class="price">Price each: <strong  itemprop="price">$' + response.pickup_date_items[i].price + '</strong></div>';
                        if (parseFloat(response.pickup_date_items[i].price) > parseFloat(response.pickup_date_items[i].bulk_price)){
                            html_string +=          '<div class="price">Group price (qty ' + response.pickup_date_items[i].minimum_bulk_price_quantity + ' or more): <strong>$' + response.pickup_date_items[i].bulk_price + '</strong></div>';
                        }
                    html_string +=              '</div>';
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
    this.populate_order_summary_table();
};

MealOrder.prototype.load_step_4 = function(){

    // format order items
    var order_items_str = '';
    for (var i = 0; i < this.order_items.length; i++){
        order_items_str += this.order_items[i].to_string() + "\n";
    }

    $.post(
        '/',
        {
            'action': 'save_meal_order',
            'order': {
                'customer_email': this.customer_email,
                'customer_name': this.customer_name,
                'customer_phone': this.customer_phone,
                'customer_notes': this.customer_notes,
                'order_items': order_items_str,
                'pickup_date_id': this.pickup_date_id,
                'sales_tax': this.order_sales_tax,
                'subtotal': this.order_subtotal,
                'total': this.order_total

            }
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
                $('#step-4-success-message').removeClass('hidden');
                $('#step-4-error-message').addClass('hidden');
            } else {
                $('#step-4-error-message').removeClass('hidden');
                $('#step-4-success-message').addClass('hidden');
            }
        },
        'json'
    );

};

MealOrder.prototype.populate_order_summary_table = function(){

    // populate items
    for (var i = 0; i < this.order_items.length; i++){
        this.order_summary_table.find('tbody').append('<tr><td>' + this.order_items[i].name + '</td><td class="text-right">' + this.order_items[i].quantity + '</td><td class="text-right">$' + this.order_items[i].price + '</td></tr>');
    }


    this.order_summary_sales_tax.html('$' + this.order_sales_tax);
    this.order_summary_subtotal.html('$' + this.order_subtotal);
    this.order_summary_total.html('$' + this.order_total);
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
            var this_name = $(this).find('.meal-item-name').text();
            var price = parseFloat($(this).data('price'));
            if (this_qty >= this_minimum_bulk_price_qty){
                price = this_bulk_price;
            }
            this_ref.order_items.push(new MealOrderItem(this_id, this_name, price, this_qty));
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
    this.order_total = parseFloat(parseFloat(this.order_subtotal) + parseFloat(this.order_sales_tax)).toFixed(2);
    // end calculate

    // update elements
    this.order_subtotal_el.html(this.order_subtotal);
    this.order_sales_tax_el.html(this.order_sales_tax);
    this.order_total_el.html(this.order_total);
    this.order_total_modal_el.html(this.order_total);
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

MealOrder.prototype.step_3_refresh_customer_info = function(){
    this.customer_email = this.customer_email_el.val();
    this.customer_name = this.customer_name_el.val();
    this.customer_notes = this.customer_notes_el.val();
    this.customer_phone = this.customer_phone_el.val();
};


MealOrder.prototype.step_3_refresh_validation = function(){
    if (
        this.customer_name_el[0].validity.valid
        && this.customer_email_el[0].validity.valid
        && this.customer_phone_el[0].validity.valid
    ){
        this.step_3_continue_btn.removeAttr('disabled');
    } else {
        this.step_3_continue_btn.attr('disabled', 'disabled');
    }
};


// order items
/**
 *
 * @param {number} id
 * @param {string} name
 * @param {number} price
 * @param {number} quantity
 * @constructor
 */
function MealOrderItem(id, name, price, quantity){
    this.id = id;
    this.name = name;
    this.price = price;
    this.quantity = quantity;
}

MealOrderItem.prototype.to_string = function(){
    return 'QTY: ' + this.quantity + ' @ ' + this.price + '/ea. ' + this.id + ': ' + this.name;
};