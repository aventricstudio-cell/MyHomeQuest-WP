/*global $, jQuery,wpestate_stripe_vars*/
function wpestate_start_stripe(recurring,modalid) {

  recurring=parseInt(recurring);

    'use strict';
    var stripe = Stripe( wpestate_stripe_vars.pub_key );
    var elements = stripe.elements({
        fonts: [
          {
            cssSrc: 'https://fonts.googleapis.com/css?family=Roboto',
          },
        ],
        locale: 'auto'
    });

  


    var card = elements.create('card', {
      iconStyle: 'solid',
      style: {
        base: {
          iconColor: '#c4f0ff',
          color: '#fff',
          fontWeight: 500,
          fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
          fontSize: '16px',
          fontSmoothing: 'antialiased',

          ':-webkit-autofill': {
            color: '#fce883',
          },
          '::placeholder': {
            color: '#87BBFD',
          },
        },
        invalid: {
          iconColor: '#FFC7EE',
          color: '#FFC7EE',
        },
      },
    });
    
    

    if(jQuery('#'+modalid+' .wpestate_form1-card').length > 0 ){
      card.mount('#'+modalid+' .wpestate_form1-card'); 
    }else{
      return;
    }

    if(recurring===0){
      var modalDiv = document.getElementById(modalid);
      card.addEventListener('change', function(event) {
          var displayError = modalDiv.querySelector('#card-errors');
          if (event.error) {
              displayError.textContent = event.error.message;
          } else {
              displayError.textContent = '';
          }
      });
      
      var cardholderName = modalDiv.querySelector('#wpestate_form_name');
      var cardButton = modalDiv.querySelector('#wpestate_stripe_form_button_sumit');
      var clientSecret = cardButton.dataset.secret;
    
    }else{
      
      card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
              displayError.textContent = event.error.message;
            } else {
              displayError.textContent = '';
            }
      });

      var cardholderName = document.getElementById('wpestate_form_name');
      var cardButton = document.getElementById('wpestate_stripe_form_button_sumit');
      var clientSecret = cardButton.dataset.secret;
    }


  

 
    
    
    
    cardButton.removeEventListener('click',function(ev){
    });
    
    
    
    
    function process_recuring(){
        stripe.createToken(card).then(function(result) {
            if (result.error) {
              // Inform the customer that there was an error.
              var errorElement = document.getElementById('card-errors');
              errorElement.textContent = result.error.message;
            } else {
              // Send the token to your server.
              stripeTokenHandler(result.token);
            }
        });
    }
    



    cardButton.addEventListener('click', function(ev) {
        ev.preventDefault();
        ev.stopPropagation();  
      
        var parent= jQuery(this).parent();
        parent.find('#wpestate_stripe_alert').show();
        
        if(recurring===1){
            process_recuring();
        }else{
            stripe.handleCardPayment(
              clientSecret, card, {
                  payment_method_data: {
                      billing_details: {name: cardholderName.value}
                  },


              }
            ).then(function(result) {
                if (result.error) {
                    // Display error.message in your UI.
                    jQuery('#wpestate_stripe_alert').empty().show().text(wpestate_stripe_vars.pay_failed);
                } else {
                    // The payment has succeeded. Display a success message.
                    payment_succes();


                }
            });
        }
      
    });

    function payment_succes(){
        setTimeout(function(){ 
            jQuery('#wpestate_stripe_alert').hide();
            jQuery('#wpestate_stripe_alert_succes').show();

            setTimeout(function(){     
               
                if(jQuery('#wpestate_stripe_booking').length > 0 ){
                    window.open(  wpestate_stripe_vars.redirect, '_self', false); 
                }else if(jQuery('#wpestate_stripe_form_button_sumit').length > 0 ){
                    location.reload();
                }else{
                    window.open(  wpestate_stripe_vars.redirect_list, '_self', false); 
                }
                
            }, 3000);

        }, 7000);
    }

    
  /*
   * 
   * @returns {undefined}
   */


  function wpestate_stripe_payment_intent_recurring(data){

    var  ajaxurl     =   wpestate_stripe_vars.admin_url + 'admin-ajax.php';
  
    stripe
    .confirmCardPayment(data.payment_intent_secret, {
      payment_method: {
        card: card,
        billing_details: {
          name: cardholderName.value
        },
      },
    })
    .then(function(result) {
      
        
        
        if(result.paymentIntent.status==="succeeded"){
             jQuery.ajax({
               type: 'POST',
               url: ajaxurl,
               dataType: 'json',
               data: {
                   'action'         :   'wpestate_stripe_reccuring_3ds',
                   'packId'         :   data.packId,
                   'responseId'     :   result.paymentIntent.id,
                   'customer_id'    :   data.customer_id,
                   'stripe_subscription_id':data.stripe_subscription_id,
               },
               success: function (data) {
 
                    payment_succes();
               },

                error: function (errorThrown) {
                }
           
        });
        }
        
       

    });

  
}





    function stripeTokenHandler(token) {

        var  ajaxurl     =   wpestate_stripe_vars.admin_url + 'admin-ajax.php';
        var packName     =   jQuery('#pack_title').val();
        var packId       =   jQuery('#pack_id').val();
        var nonce        =   jQuery('#wpestate_payments_nonce').val();
        var packValue    =   jQuery('#pay_ammout').val();
        var onetime      =   0;
        if( jQuery('#pack_recuring').prop('checked') ){
            onetime=1;
        }
        
        jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        dataType: 'json',
        data: {
            'action'        :   'wpestate_stripe_recurring',
            'packName'      :   packName,
            'packId'        :   packId,
            'token'         :   token.id,
            'onetime'       :   onetime,
            'packValue'     :   packValue,
            'security'      :   nonce
        },
        success: function (data) {
            if(data.answer==="complete"){
              payment_succes();        
            }else if(data.answer==='3ds'){
              wpestate_stripe_payment_intent_recurring(data);
            }else{
              jQuery('#wpestate_stripe_alert').empty().show().text(wpestate_stripe_vars.pay_failed);
            }
            
            
        },
        error: function (errorThrown) {
        }
    });//end ajax    
        
    }

};