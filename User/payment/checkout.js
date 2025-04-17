document.addEventListener("DOMContentLoaded", function() {
    // Initialize Stripe
    const stripe = Stripe("pk_test_51R3yBQQZPLk7FzRY3uO9YLeLKEbmLgOWzlD43uf0xHYeHdVC13kMzpCw5zhRPnp215QEwdZz7F9qmeMT6dv2ZmC600HNBheJIT");
    const elements = stripe.elements();
    
    // Create card element
    const cardElement = elements.create("card", {
        style: {
            base: {
                fontSize: "16px",
                color: "#32325d",
                "::placeholder": { color: "#aab7c4" },
                fontSmoothing: "antialiased",
            },
            invalid: {
                color: "#fa755a",
                iconColor: "#fa755a"
            }
        }
    });
    
    // Mount the card element
    cardElement.mount("#card-element");
    
    // Get DOM elements
    const form = document.getElementById("payment-form");
    const payButton = document.getElementById("submit");
    const errorElement = document.getElementById("error-message");
    const successElement = document.getElementById("success-message");
    const loadingElement = document.getElementById("loading");
    
    // Handle form submission
    form.addEventListener("submit", async function(event) {
        event.preventDefault();
        
        // Disable button and show loading state
        payButton.disabled = true;
        payButton.textContent = "Processing...";
        loadingElement.style.display = "block";
        clearMessages();
        
        // Get order ID from hidden input
        const orderId = document.getElementById("order-id").value;
        
        // Create payment method
        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: "card",
            card: cardElement,
        });
        
        if (error) {
            // Handle payment creation error
            showError(error.message);
            payButton.disabled = false;
            payButton.textContent = "Pay";
            loadingElement.style.display = "none";
            return;
        }
        
        try {
            // Send payment method ID to server
            const response = await fetch("charge.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    payment_method_id: paymentMethod.id,
                    order_id: orderId
                }),
            });
            
            // Parse response
            const result = await response.json();
            
            if (result.success) {
                // Payment successful
                showSuccess("Payment successful! Redirecting...");
                // Redirect to success page
                window.location.href = "order_success.php?order_id=" + result.order_id;
            } else {
                // Payment failed
                showError(result.error || "Payment failed. Please try again.");
                payButton.disabled = false;
                payButton.textContent = "Pay";
            }
        } catch (error) {
            // Handle network or other errors
            showError("An error occurred. Please try again.");
            payButton.disabled = false;
            payButton.textContent = "Pay";
        } finally {
            loadingElement.style.display = "none";
        }
    });
    
    // Error handling functions
    function showError(message) {
        errorElement.textContent = message;
        errorElement.style.display = "block";
    }
    
    function showSuccess(message) {
        successElement.textContent = message;
        successElement.style.display = "block";
    }
    
    function clearMessages() {
        errorElement.style.display = "none";
        successElement.style.display = "none";
    }
    
    // Handle card element events
    cardElement.addEventListener('change', function(event) {
        if (event.error) {
            showError(event.error.message);
        } else {
            clearMessages();
        }
    });
    
    // Alternative payment option click handler
    const paymentOption = document.querySelector('.payment-option');
    if (paymentOption) {
        paymentOption.addEventListener('click', function() {
            // Implement alternative payment method here
            alert("Alternative payment method coming soon!");
        });
    }
});
