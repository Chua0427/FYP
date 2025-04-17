/**
 * VeroSports Shopping Cart JavaScript
 * Handles all cart interactions including quantity updates and item removal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all cart functionality
    initCartFunctions();

    // Add event listeners to store checkboxes
    initStoreCheckboxes();
});

/**
 * Initialize all cart functions
 */
function initCartFunctions() {
    // Handle quantity update buttons
    initQuantityControls();
    
    // Handle remove item buttons
    initRemoveButtons();
    
    // Handle checkout button animation
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        checkoutBtn.addEventListener('mouseup', function() {
            this.style.transform = 'scale(1)';
        });
        
        checkoutBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
}


//Initialize quantity control buttons and input validation
 
function initQuantityControls() {
    // Handle quantity buttons (plus and minus)
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
            let value = parseInt(input.value);
            
            if (this.classList.contains('minus')) {
                value = Math.max(1, value - 1);
            } else {
                value++;
            }
            
            input.value = value;
            
            // Update quantity via form
            document.getElementById('update-cart-id').value = cartId;
            document.getElementById('update-quantity').value = value;
            document.getElementById('update-form').submit();
        });
    });
    
    // Handle direct input on quantity fields
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.name.match(/quantity\[(\d+)\]/)[1];
            let value = parseInt(this.value);
            
            // Ensure value is at least 1
            if (isNaN(value) || value < 1) {
                value = 1;
                this.value = value;
            }
            
            // Update quantity
            updateQuantity(cartId, value);
        });
        
        // Prevent non-numeric input
        input.addEventListener('keypress', function(e) {
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Send quantity update to server
 * @param {string} cartId - The cart item ID
 * @param {number} quantity - The new quantity
 */
function updateQuantity(cartId, quantity) {
    // Get form element
    const form = document.getElementById('update-form');
    
    // Set form values
    document.getElementById('update-cart-id').value = cartId;
    document.getElementById('update-quantity').value = quantity;
    
    // Submit the form
    form.submit();
}

/**
 * Initialize remove item buttons
 */
function initRemoveButtons() {
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const cartId = this.dataset.cartId;
            
            if (confirm('Are you sure you want to remove this item?')) {
                document.getElementById('remove-cart-id').value = cartId;
                document.getElementById('remove-form').submit();
            }
        });
    });
}

/**
 * Initialize store checkboxes to select/deselect all items in a store
 */
function initStoreCheckboxes() {
    document.querySelectorAll('.store h3 input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const storeDiv = this.closest('.store');
            const isChecked = this.checked;
            
            // Update visual feedback when toggling store checkbox
            storeDiv.style.opacity = isChecked ? '1' : '0.6';
            
            // Get all product checkboxes in this store
            const productCheckboxes = storeDiv.querySelectorAll('.product input[type="checkbox"]');
            productCheckboxes.forEach(productCheckbox => {
                productCheckbox.checked = isChecked;
            });
            
            // Note: In a full implementation, you would update a hidden form field
            // with the selected items to process during checkout
        });
    });
}

/**
 * Animate button press for visual feedback
 * @param {HTMLElement} button - The button element
 * @param {string} type - Button type ('plus' or 'minus')
 */
function animateButton(button, type) {
    button.classList.add('active');
    setTimeout(() => {
        button.classList.remove('active');
    }, 200);
}
