jQuery(document).ready(function($) {
    createStripeForm();
});


function createStripeForm()
{
    const stripe = Stripe( stripe_config.stripe_key );
    var name_on_card = document.getElementById("name_on_card").value;
    var email = document.getElementById("email").value;
    var username = document.getElementById("username_hidden").value;
    var amount = document.getElementById("amount_hidden").value;
    var customer_id = '';

    // Fetch the PaymentIntent client secret from the server
    fetch(stripe_config.wp_json + '/stripe/pi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            "amount": amount,
            "currency": "USD",
            "name": name_on_card,
            "email": email
            })
        })
        .then(response => response.json())
        .then(data => {
            const clientSecret = data.clientSecret;
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');

            customer_id = data.customer_id;

            // Handle form submission
            document.getElementById('payment-form').addEventListener('submit', async (event) => {
                event.preventDefault();

                const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: cardElement,
                    }
                });

                if (error) {
                    // Display error to the customer
                    console.error(error.message);
                    document.getElementById("card-errors").innerHTML = error.message;
                } else if (paymentIntent.status === 'succeeded') {
                    // Payment succeeded - send data to save_customer.php
                    saveCustomerId( customer_id, name_on_card, email, paymentIntent.payment_method, username );
                }
            });
        });
}

function saveCustomerId( customer_id, name, email, payment_method, username )
{
    fetch(stripe_config.wp_json + '/customer/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            "customer_id": customer_id,
            "name": name,
            "email": email,
            "payment_method": payment_method,
            "username": username
            })
        })
        .then(response => response.json())
        .then(data => {

            if (data.success === true)
            {
                window.location = data.success_url;
            }
            else
            {
                document.getElementById("card-errors").innerHTML = data.message;
            }

        });
}

function createStripe_WPCustomer( fname, lname, email, password, payment_method )
{
    fetch(stripe_config.wp_json + '/customer/createv2', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            "fname": fname,
            "lname": lname,
            "email": email,
            "password": password,
            "payment_method": payment_method
            })
        })
        .then(response => response.json())
        .then(data => {

            if (data.success === true)
            {
                window.location = data.success_url;
            }
            else
            {
                document.getElementById("card-errors").innerHTML = data.message;
            }

        });
}