
// stripe integration in checkout page
function mystripe(stripe) {
   // Set your publishable key: remember to change this to your live publishable key in production
	// var stripe = Stripe('pk_test_vwUeU2SOiAQHXl6RPOa7lX7j00AR9Ts6dq');
	// console.log('stripe: ', stripe); // ok
	var elements = stripe.elements();

	var style = {
	    base: {
	      color: "#32325d",
	      fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
	      fontSmoothing: "antialiased",
	      fontSize: "16px",
	      "::placeholder": {
	        color: "#aab7c4"
	      }
	    },
	    invalid: {
	      color: "#fa755a",
	      iconColor: "#fa755a"
	    }
	};

	var card = elements.create("card", { 
		style: style,
		// hidePostalCode: true
	});
	
	card.mount("#card-element");

	// Handle real-time validation errors from the card Element.
	card.addEventListener('change', function(event) {
	  var displayError = document.getElementById('card-errors');

	  if (event.error) {
	    displayError.textContent = event.error.message;
	  } else {
	    displayError.textContent = '';
	  }
	});

	// Handle form submission.
	// ****************************************************************************************
	// ****************************************************************************************
	// var form = document.getElementById('creditCardForm');

	// $('#submitCreditPaymentBtn').click(function(event) {
	$('#pay-me').click(function(event) {
	// form.addEventListener('submit', function(event) {
		event.preventDefault();
		console.log('in pay_me');

		stripe.createToken(card).then(function(result) {
			if (result.error) {
				console.log('stripe.createToken error');
				// Inform the user if there was an error.
				var errorElement = document.getElementById('card-errors');
				errorElement.textContent = result.error.message;
			} else {
				console.log('stripe.createToken success');
				// Send the token to your server.
				stripeTokenHandler(result.token);
			}
		});

	  /*stripe.confirmCardPayment(clientSecret, {
	    payment_method: {
	      card: card,
	      billing_details: {
	        name: 'Jenny Rosen'
	      }
	    }
	  }).then(function(result) {
	    if (result.error) {
	      // Show error to your customer (e.g., insufficient funds)
	      console.log(result.error.message);
	    } else {
	      // The payment has been processed!
	      if (result.paymentIntent.status === 'succeeded') {
	        // Show a success message to your customer
	        // There's a risk of the customer closing the window before callback
	        // execution. Set up a webhook or plugin to listen for the
	        // payment_intent.succeeded event that handles any business critical
	        // post-payment actions.
	      }
	    }
	  });*/
	});

	return card;
}

// Submit the form with the token ID.
function stripeTokenHandler(token) {
  // Insert the token ID into the form so it gets submitted to the server
  // ****************************************************************************************
  // ****************************************************************************************
  var form = document.getElementById('creditCardForm');
  var hiddenInput = document.createElement('input');
  // console.log('in stripeTokenHandler');
  
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.prepend(hiddenInput);

  // Submit the form
  // form.submit();
}

export { mystripe, stripeTokenHandler }