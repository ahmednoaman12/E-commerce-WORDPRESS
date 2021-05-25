jQuery(function ($) {
    'use strict';
   
    var stripe = Stripe(eh_stripe_checkout_params.key);

    var eh_checkout_form = {

        init: function(  ) {

            window.addEventListener( 'hashchange', eh_checkout_form.Change );
        },

        Change: function() {

            var partials = window.location.hash.match( /response=(.*)/ );

            if ( ! partials ) {
                return;
            }
            
            var obj = JSON.parse(window.atob(partials[1]));
            var session_id = obj.session_id;
            
            stripe.redirectToCheckout({
                sessionId: session_id

            }).then(function (result) {
                
                console.log(result.error.message);

            });

            // // // Cleanup the URL
            window.location.hash = '';
           
        },

    };
    eh_checkout_form.init( );

});