jQuery(function ($) {
    'use strict';
  
  
    var stripe = Stripe(eh_payment_request_params.key);
  
    var eh_payment_request_gen = {
  
      eh_generate_cart: function () {
  
        var data = {
          '_wpnonce': eh_payment_request_params.eh_payment_request_cart_nonce
        };
          
        $.ajax({
          type: 'POST',
          data:data,
          url: eh_payment_request_params.wc_ajaxurl.toString().replace( '%%change_end%%', "eh_spg_gen_payment_request_button_cart"),
          success: function (response) {
            eh_payment_request_gen.startPaymentRequest( response );
          }
        });
      }, 
  
      ProcessPaymentMethod: function( PaymentMethod ) {
  
        var data = eh_payment_request_gen.OrderDetails( PaymentMethod );
  
              return  $.ajax( {
                  type:    'POST',
                  data:    data,
          dataType: 'json',
          url: eh_payment_request_params.wc_ajaxurl.toString().replace( '%%change_end%%', "eh_spg_gen_payment_request_create_order"),
        } );
  
      },
  
      OrderDetails: function( evt ) {
  
              var payment_method   = evt.paymentMethod;
              var email            = payment_method.billing_details.email;
              var phone            = payment_method.billing_details.phone;
              var billing          = payment_method.billing_details.address;
              var name             = evt.payerName;
              var shipping         = evt.shippingAddress;
              var data = {
               _wpnonce:                   eh_payment_request_params.eh_checkout_nonce,
                  billing_first_name:        null !== name ? name.split( ' ' ).slice( 0, 1 ).join( ' ' ) : '',
                  billing_last_name:         null !== name ? name.split( ' ' ).slice( 1 ).join( ' ' ) : '',
                  billing_company:           '',
                  billing_email:             null !== email   ? email : evt.payerEmail,
                  billing_phone:             null !== phone   ? phone : evt.payerPhone.replace( '/[() -]/g', '' ),
                  billing_country:           null !== billing ? billing.country : '',
                  billing_address_1:         null !== billing ? billing.line1 : '',
                  billing_address_2:         null !== billing ? billing.line2 : '',
                  billing_city:              null !== billing ? billing.city : '',
                  billing_state:             null !== billing ? billing.state : '',
                  billing_postcode:          null !== billing ? billing.postal_code : '',
                  shipping_first_name:       '',
                  shipping_last_name:        '',
                  shipping_company:          '',
                  shipping_country:          '',
                  shipping_address_1:        '',
                  shipping_address_2:        '',
                  shipping_city:             '',
                  shipping_state:            '',
                  shipping_postcode:         '',
                  shipping_method:           [ null === evt.shippingOption ? null : evt.shippingOption.id ],
                  order_comments:            '',
                  payment_method:            'eh_stripe_pay',
                  ship_to_different_address: 1,
                  terms:                     1,
          eh_stripe_pay_token:       payment_method.id,
          eh_stripe_card_type:       payment_method.card.brand,
              };
  
              if ( shipping ) {
                  data.shipping_first_name = shipping.recipient.split( ' ' ).slice( 0, 1 ).join( ' ' );
                  data.shipping_last_name  = shipping.recipient.split( ' ' ).slice( 1 ).join( ' ' );
                  data.shipping_company    = shipping.organization;
                  data.shipping_country    = shipping.country;
                  data.shipping_address_1  = typeof shipping.addressLine[0] === 'undefined' ? '' : shipping.addressLine[0];
                  data.shipping_address_2  = typeof shipping.addressLine[1] === 'undefined' ? '' : shipping.addressLine[1];
                  data.shipping_city       = shipping.city;
                  data.shipping_state      = shipping.region;
                  data.shipping_postcode   = shipping.postalCode;
              }
  
              return data;
          },
      
      startPaymentRequest: function (cart) {
  
        if(eh_payment_request_params.product){
          var paymentdetails = {
            country: eh_payment_request_params.country_code,
            currency: eh_payment_request_params.currency_code,
            total: {
              label: eh_payment_request_params.product_data.total.label,
              amount: parseInt(eh_payment_request_params.product_data.total.amount),
                
            },
            requestPayerName: true,
            requestPayerEmail: true,
            requestPayerPhone: true,
            requestShipping: eh_payment_request_params.product_data.needs_shipping,
            displayItems: eh_payment_request_params.product_data.displayItems,
            
          };
        }else{
          var paymentdetails = {
  
          country: eh_payment_request_params.country_code,
          currency: eh_payment_request_params.currency_code,
          total: {
            label: eh_payment_request_params.label,
            amount: parseInt(cart.total),
              
          },
          displayItems: cart.line_items.displayItems,
          requestPayerName: true,
          requestPayerEmail: true,
          requestPayerPhone: true,
          requestShipping: ('yes' === eh_payment_request_params.needs_shipping) ? true : false,
         
          }
        }
  
        var paymentRequest = stripe.paymentRequest(paymentdetails);
  
        var elements = stripe.elements();
        var prButton = elements.create('paymentRequestButton', {
          paymentRequest: paymentRequest,
          style: {
                      paymentRequestButton: {
                          type: eh_payment_request_params.button_type,
                          theme: eh_payment_request_params.button_theme,
                          height: eh_payment_request_params.button_height + 'px'
                      },
                  }
        });
       
        paymentRequest.canMakePayment().then(function(result) {
              
          if (result) {
              
            if (result['applePay'] == true) {
              // document.getElementById('eh-stripe-payment-request-button').style.display = 'none';
              //show applepay button
              $('.apple-pay-button-div').show();               
              $('.woocommerce-checkout .apple-pay-button').css('visibility', 'visible');
              
              $('#eh-payment-request-button-seperator').hide();
              
            }else{
              $('.apple-pay-button-div').hide();
              if(eh_payment_request_params.product){ 
                prButton.on( 'click', function( evt ) 
                {
                  var applepay = '';
                  eh_payment_request_gen.add_to_cart(evt,paymentRequest,applepay);
                });
              }
              prButton.mount('#eh-stripe-payment-request-button');
            }
          } else {
            document.getElementById('eh-stripe-payment-request-button').style.display = 'none';
            
            $('#eh-payment-request-button-seperator').hide();
            $('.apple-pay-button-div').hide();
          }
        });
  
        $(document.body).on('click', '.apple-pay-button', function (e) {
          e.preventDefault();
         
          if(eh_payment_request_params.product){ 
            var applepay = 1;
            eh_payment_request_gen.add_to_cart(e,paymentRequest,applepay);
          }else{
            paymentRequest.show();
          }
        });
        
        paymentRequest.on( 'shippingaddresschange', function( evt ) {
            
          $.when( eh_payment_request_gen.updateShippingOptions( paymentdetails, evt.shippingAddress ) ).then( function( response ) {
        
            evt.updateWith( { status: response.result, shippingOptions: response.shipping_options, total: response.total, displayItems: response.displayItems } );
            
          });
        });
  
  
        paymentRequest.on( 'shippingoptionchange', function( evt ) {
          
          $.when( eh_payment_request_gen.updateShippingDetails( paymentdetails, evt.shippingOption ) ).then( function( response ) {
              
            if ( 'success' === response.result ) {
              evt.updateWith( { status: 'success', total: response.total, displayItems: response.displayItems } );
            }
  
            if ( 'fail' === response.result ) {
              evt.updateWith( { status: 'fail' } );
            }
          });                                                
        });
  
        paymentRequest.on('paymentmethod', function(evt) {
          
          $.when( eh_payment_request_gen.ProcessPaymentMethod( evt) ).then( function( response ) {
            if ( 'success' === response.result ) {
              eh_payment_request_gen.completePayment( evt, response.redirect );
            } else {
              eh_payment_request_gen.paymentFailure( evt, response.messages );
            }
          });
        });
      },
  
      add_to_cart: function(e,paymentRequest,applepay){
       
        if ( $( '.single_add_to_cart_button' ).is( '.disabled' ) ) {
          e.preventDefault();
         
          if ( $( '.single_add_to_cart_button' ).is('.wc-variation-is-unavailable') ) {
            window.alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
          } else if ( $( '.single_add_to_cart_button' ).is('.wc-variation-selection-needed') ) {
            window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
          }
          return;
        } 
         
        eh_payment_request_gen.add_to_cart_ajax_call();
  
        if(applepay){
          
          paymentRequest.show();
        }
      },
  
      add_to_cart_ajax_call: function(){
  
        var qty = $('.qty').val();
        if(!qty){
          qty = $("input[name=quantity]").val();  
        }
  
        var product_id = $( '.button.single_add_to_cart_button' ).val();
        if(! product_id){
          product_id = $("input[name=add-to-cart]").val();  
        }
        if ( $( '.single_variation_wrap' ).length ) {
          product_id = $( '.single_variation_wrap' ).find( 'input[name="product_id"]' ).val();
          var variation_id = $( '.single_variation_wrap' ).find( 'input[name="variation_id"]' ).val();
        }
        var data = {
          qty: qty,
          product_id: product_id,
          variation_id: variation_id ? variation_id : 0,
          '_wpnonce': eh_payment_request_params.eh_add_to_cart_nonce
  
        };
  
        return  $.ajax( {
          type:    'POST',
          data:    data,
          url:     eh_payment_request_params.wc_ajaxurl.toString().replace( '%%change_end%%', "eh_spg_add_to_cart"),
        });
  
      },
  
      updateShippingOptions: function( details, address ) {
            
        var data = {
          '_wpnonce': eh_payment_request_params.eh_payment_request_get_shipping_nonce,
          country:   address.country,
          state:     address.region,
          postcode:  address.postalCode,
          city:      address.city,
          address:   typeof address.addressLine[0] === 'undefined' ? '' : address.addressLine[0],
          address_2: typeof address.addressLine[1] === 'undefined' ? '' : address.addressLine[1],
        };
  
        return  $.ajax( {
          type:    'POST',
          data:    data,
          url:     eh_payment_request_params.wc_ajaxurl.toString().replace( '%%change_end%%', "eh_spg_payment_request_get_shippings"),
        });
      },
  
      updateShippingDetails: function( details, shippingOption ) {
        var data = {
          '_wpnonce' : eh_payment_request_params.eh_payment_request_update_shipping_nonce,
        
          shipping_method: [ shippingOption.id ],
  
        };
  
        return  $.ajax( {
          type: 'POST',
          data: data,
          url:  eh_payment_request_params.wc_ajaxurl.toString().replace( '%%change_end%%', "eh_spg_payment_request_update_shippings")
        } );
        
      },
  
      completePayment: function( payment, url ) {
        eh_payment_request_gen.block();
  
        payment.complete( 'success' );
  
        // Success, then redirect to the Thank You page.
        window.location = url;
      },
  
      block: function() {
        $.blockUI( {
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6
          }
        } );
      },
  
      paymentFailure: function( payment, message ) {
        payment.complete( 'fail' );
  
        var $target = $( '.woocommerce-notices-wrapper:first' ) || $( '.cart-empty' ).closest( '.woocommerce' ) || $( '.woocommerce-cart-form' );
  
        $( '.woocommerce-error' ).remove();
          
        $target.append( message );
        $(window).scrollTop(0);
  
      },
  
      init: function() {
        if(eh_payment_request_params.product){
          eh_payment_request_gen.startPaymentRequest( '' );
        }
            
        eh_payment_request_gen.eh_generate_cart();
  
      },
    };
  
    eh_payment_request_gen.init();
  
    
    $(document.body).on('updated_cart_totals', function () {
      eh_payment_request_gen.init();
    });
  
    
    $(document.body).on('updated_checkout', function () {
      eh_payment_request_gen.init();
    });
  
  });