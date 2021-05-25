jQuery( function( $ ) {
    'use strict';
    

try {
    var stripe = Stripe( eh_stripe_val.key );
} catch( error ) {
    console.log( error );
    return;
}

var stripe_elements_options = Object.keys( eh_stripe_val.elements_options ).length ? eh_stripe_val.elements_options : {},
    elements                = stripe.elements( stripe_elements_options ),
    stripe_card,
    stripe_exp,
    stripe_cvc;

    /**
     * Object to handle Stripe payment forms.
     */
    var eh_stripe_form = {
        

        onCCFormChange: function() {
            eh_stripe_form.reset();
        },

        reset: function() {
            $( '.wc-stripe-error, .stripe-source, .stripe_token' ).remove();
                eh_stripe_form.stripe_submit = false;
        },


        updateCardBrand: function( brand ) {
            var brandClass = {
                'visa': 'eh-stripe-visa-brand',
                'mastercard': 'eh-stripe-mastercard-brand',
                'amex': 'eh-stripe-amex-brand',
                'discover': 'eh-stripe-discover-brand',
                'diners': 'eh-stripe-diners-brand',
                'jcb': 'eh-stripe-jcb-brand',
                'unknown': 'stripe-credit-card-brand'
            };

            var imageElement = $( '.eh-stripe-card-brand' ),
                imageClass = 'eh-stripe-credit-card-brand';

            if ( brand in brandClass ) {
                imageClass = brandClass[ brand ];
            }

            // Remove existing card brand class.
            $.each( brandClass, function( index, el ) {
                imageElement.removeClass( el );
            } );

            imageElement.addClass( imageClass );
        },

        unmountElements: function() {
            
            if ( 'yes' === eh_stripe_val.enabled_inline_form ) {
				stripe_card.unmount( '#eh-stripe-card-element' );

			}else{
                stripe_card.unmount( '#eh-stripe-card-element' );
                stripe_exp.unmount( '#eh-stripe-exp-element' );
                stripe_cvc.unmount( '#eh-stripe-cvc-element' );
            }

        },
        mountElements: function() {
            if ( ! $( '#eh-stripe-card-element' ).length ) {
                return;
            }

            if ( 'yes' === eh_stripe_val.enabled_inline_form ) {
				return stripe_card.mount( '#eh-stripe-card-element' );
			}
            
            stripe_card.mount( '#eh-stripe-card-element' );
            stripe_exp.mount( '#eh-stripe-exp-element' );
            stripe_cvc.mount( '#eh-stripe-cvc-element' );
        },


        createElements: function() {
            var elementStyles = {
                base: {
                    iconColor: '#666EE8',
                    color: '#31325F',
                    fontSize: '15px',
                    '::placeholder': {
                        color: '#CFD7E0',
                    }
                }
            };

            var elementClasses = {
                focus: 'focused',
                empty: 'empty',
                invalid: 'invalid',
            };
            if ( 'yes' === eh_stripe_val.enabled_inline_form ) {
				stripe_card = elements.create( 'card', { style: elementStyles, hidePostalCode: (eh_stripe_val.inline_postalcode ? true : false) } );

				stripe_card.addEventListener( 'change', function( event ) {
					eh_stripe_form.onCCFormChange();

					if ( event.error ) {
						$( document.body ).trigger( 'stripeError', event );
					}
				} );
			} else {

                stripe_card = elements.create( 'cardNumber', { style: elementStyles, classes: elementClasses } );
                stripe_exp  = elements.create( 'cardExpiry', { style: elementStyles, classes: elementClasses } );
                stripe_cvc  = elements.create( 'cardCvc', { style: elementStyles, classes: elementClasses } );

                stripe_card.addEventListener( 'change', function( event ) {
                    eh_stripe_form.onCCFormChange();

                    eh_stripe_form.updateCardBrand( event.brand );

                    if ( event.error ) {
                        $( document.body ).trigger( 'stripeError', event );
                    }
                } );

                stripe_exp.addEventListener( 'change', function( event ) {
                    eh_stripe_form.onCCFormChange();

                    if ( event.error ) {
                        $( document.body ).trigger( 'stripeError', event );
                    }
                } );

                stripe_cvc.addEventListener( 'change', function( event ) {
                    eh_stripe_form.onCCFormChange();

                    if ( event.error ) {
                        $( document.body ).trigger( 'stripeError', event );
                    }
                } );
            }

            /**
             * Only in checkout page we need to delay the mounting of the
             * card as some AJAX process needs to happen before we do.
             */
            if ( 'yes' === eh_stripe_val.is_checkout ) {
                $( document.body ).on( 'updated_checkout', function() {
                    // Don't mount elements a second time.
                    if ( stripe_card ) {
                        eh_stripe_form.unmountElements();
                    }

                    eh_stripe_form.mountElements();
                } );

            } else if ( $( 'form#add_payment_method' ).length || $( 'form#order_review' ).length ) {
                    eh_stripe_form.mountElements();
            }
        },

        /**
         * Initialize e handlers and UI state.
         */
        init: function(  ) {

            if ( $( 'form.woocommerce-checkout' ).length ) {
                this.form = $( 'form.woocommerce-checkout' );
            }

            if ( $( 'form#order_review' ).length ) {
                this.form = $( 'form#order_review' );
            }

            // add payment method page
            if ( $( 'form#add_payment_method' ).length ) {
                this.form = $( 'form#add_payment_method' );
            }

            this.stripe_submit = false;
            
            eh_stripe_form.createElements(); // create elements into inline form.

            // handle payment intend
            window.addEventListener( 'hashchange', eh_stripe_form.onHashChange );
            eh_stripe_form.maybeConfirmIntent();


            $( this.form )
                
                .on( 'click', '#place_order', this.onSubmit )

                // WooCommerce lets us return a false on checkout_place_order_{gateway} to keep the form from submitting
                .on( 'submit checkout_place_order_stripe' );

        },

        isStripeChosen: function() {
            return $( '#payment_method_eh_stripe_pay' ).is( ':checked' );
        },
        getSelectedPaymentElement: function() {
            return $( '.payment_methods input[name="payment_method"]:checked' );
        },

        isStripeModalNeeded: function( e ) {     
            
            if ( ! eh_stripe_form.isStripeChosen() ) {
                return false;
            }

            if ( $( 'input#terms' ).length === 1 && $( 'input#terms:checked' ).length === 0 ) {
                return false;
            }

            if ( $( '#createaccount' ).is( ':checked' ) && $( '#account_password' ).length && $( '#account_password' ).val() === '' ) {
                return false;
            }

            //check to see if we need to validate shipping address
            if ( $( '#ship-to-different-address-checkbox' ).is( ':checked' ) ) {
                var $required_inputs = $( '.woocommerce-billing-fields .validate-required, .woocommerce-shipping-fields .validate-required' );
            } else {
                var $required_inputs = $( '.woocommerce-billing-fields .validate-required' );
            }

            if ( $required_inputs.length ) {
                var required_error = false;

                $required_inputs.each( function() {
                    if ( $( this ).find( 'input.input-text, select' ).not( $( '#account_password, #account_username' ) ).val() === '' ) {
                        required_error = true;
                    }
                });

                if ( required_error ) {
                    return false;
                }
            }

            return true;
        },

        block: function() {
            eh_stripe_form.form.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        },

        unblock: function() {
            eh_stripe_form.form.unblock();
        },

        onClose: function() {
            eh_stripe_form.unblock();
        },

        onSubmit: function( e ) {
            if ( eh_stripe_form.isStripeModalNeeded() ) {
                e.preventDefault();
            
                var $form = eh_stripe_form.form,
                    $data = $( '#eh-stripe-pay-data' );
                    
                        stripe.createPaymentMethod (
                            'card',
                            stripe_card
                        ).then(function(result) {
                            


                      if (result.error) {
                        $('.stripe-source-errors').html(
                            '<ul class="woocommerce_error woocommerce-error eh-stripe-error"><li>'+ result.error.message +'</li></ul>');
                      } else {
                        

                        $form.find( 'input.eh_stripe_pay_token' ).remove();
                        $form.find( 'input.eh_stripe_card_type' ).remove();
                        $form.find( 'input.eh_stripe_pay_currency' ).remove();
                        
                        $form.append( '<input type="hidden" class="eh_stripe_pay_token" name="eh_stripe_pay_token" value="' + result.paymentMethod.id + '"/>' );
                        $form.append( '<input type="hidden" class="eh_stripe_pay_currency" name="eh_stripe_pay_currency" value="' + $data.data( 'currency' ) + '"/>' );
                        $form.append( '<input type="hidden" class="eh_stripe_pay_amount" name="eh_stripe_pay_amount" value="' + $data.data( 'amount' ) + '"/>' );
                        if(result.paymentMethod.type === "card")
                        {
                            $form.append( '<input type="hidden" class="eh_stripe_card_type" name="eh_stripe_card_type" value="' + result.paymentMethod.card.brand + '"/>' );
                        }
                        else
                        {
                            $form.append( '<input type="hidden" class="eh_stripe_card_type" name="eh_stripe_card_type" value="other"/>' );
                        }



                            eh_stripe_form.stripe_submit = true;
                            $form.submit();
                      }
                    });
                    return false;


                    
                }

                return true;
        },

        onHashChange: function() {
            
            var partials = window.location.hash.match( /^#?confirm-pi-([^:]+):(.+)$/ );

            if ( ! partials || 3 > partials.length ) {
                return;
            }

            var intentClientSecret = partials[1];
            var redirectURL        = decodeURIComponent( partials[2] );

            // Cleanup the URL
            window.location.hash = '';

            eh_stripe_form.openIntentModal( intentClientSecret, redirectURL );
        },
        maybeConfirmIntent: function() {
            if ( ! $( '#eh-stripe-intent-id' ).length || ! $( '#eh-stripe-intent-return' ).length ) {
                return;
            }

            var intentSecret = $( '#eh-stripe-intent-id' ).val();
            var returnURL    = $( '#eh-stripe-intent-return' ).val();

            eh_stripe_form.openIntentModal( intentSecret, returnURL, true );
        },

        openIntentModal: function( intentClientSecret, redirectURL, alwaysRedirect ) {
            stripe.handleCardPayment( intentClientSecret )
                .then( function( response ) {
                    if ( response.error ) {
                        throw response.error;
                    }

                    if ( 'requires_capture' !== response.paymentIntent.status && 'succeeded' !== response.paymentIntent.status ) {
                        return;
                    }

                    window.location = redirectURL;
                } )
                .catch( function( error ) {
                    $('.stripe-source-errors').html(
                        '<ul class="woocommerce_error woocommerce-error eh-stripe-error"><li> '+error.message+'</li></ul>');
                    if ( alwaysRedirect ) {
                        return window.location = redirectURL;
                    }
                    
                    if ( $( '.eh-stripe-error' ).length ) {
                        $( 'html, body' ).animate({
                            scrollTop: ( $( '.eh-stripe-error' ).offset().top - 200 )
                        }, 200 );
                    }
                    eh_stripe_form.unblock();
                    $.unblockUI(); // If arriving via Payment Request Button.


                    eh_stripe_form.form && eh_stripe_form.form.removeClass( 'processing' );

                    // Report back to the server.
                    $.get( redirectURL + '&is_ajax' );
                } );
        }


    };

    eh_stripe_form.init( );
} );
function getCookie(name) {
    var cookieName = name + "=";
    var cookieArray = document.cookie.split(';');

    for (var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i];
        while (cookie.charAt(0) == ' ') {
            cookie = cookie.substring(1, cookie.length);
        }
        if (cookie.indexOf(cookieName) == 0) {
            return cookie.substring(cookieName.length, cookie.length);
        }
        return null;
    }
}