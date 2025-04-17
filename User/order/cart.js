/**
 * VeroSports Shopping Cart JavaScript
 * Handles all cart interactions including quantity updates and item removal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all cart functionality
    initCartFunctions();

    // Add event listeners to store checkboxes
    initStoreCheckboxes();

    // Get CSRF token
    const csrfToken = getCsrfToken();
    
    // Handle quantity change
    const minusButtons = document.querySelectorAll('.quantity-btn.minus');
    const plusButtons = document.querySelectorAll('.quantity-input.plus');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const removeButtons = document.querySelectorAll('.remove-btn');
    
    minusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
            let value = parseInt(input.value, 10);
            value = Math.max(1, value - 1);
            input.value = value;
            
            // Update quantity via AJAX
            updateQuantity(cartId, value);
        });
    });
    
    plusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
            let value = parseInt(input.value, 10);
            value += 1;
            input.value = value;
            
            // Update quantity via AJAX
            updateQuantity(cartId, value);
        });
    });
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.name.match(/\[(\d+)\]/)[1];
            let value = parseInt(this.value, 10);
            value = Math.max(1, value); // Ensure minimum value is 1
            this.value = value;
            
            // Update quantity via AJAX
            updateQuantity(cartId, value);
        });
    });
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item?')) {
                const cartId = this.getAttribute('data-cart-id');
                
                // Set the cart ID in the hidden form and submit
                document.getElementById('remove-cart-id').value = cartId;
                
                // Add CSRF token to the form if not already present
                const removeForm = document.getElementById('remove-form');
                ensureFormHasCsrfToken(removeForm, csrfToken);
                
                removeForm.submit();
            }
        });
    });
});

/**
 * Initialize all cart functions
 */
function initCartFunctions() {
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
    
    // Add CSRF token to the form if not already present
    ensureFormHasCsrfToken(form, csrfToken);
    
    // Submit the form
    form.submit();
}

/**
 * Get CSRF token from the page
 * @returns {string|null} CSRF token or null if not found
 */
function getCsrfToken() {
    // Try to get token from hidden input
    const tokenInput = document.querySelector('input[name="csrf_token"]');
    if (tokenInput) {
        return tokenInput.value;
    }
    
    // Try to get from meta tag
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (tokenMeta) {
        return tokenMeta.getAttribute('content');
    }
    
    return null;
}

/**
 * Ensure a form has the CSRF token
 * @param {HTMLFormElement} form - The form to check
 * @param {string} token - The CSRF token value
 */
function ensureFormHasCsrfToken(form, token) {
    if (!token) return;
    
    let tokenInput = form.querySelector('input[name="csrf_token"]');
    if (!tokenInput) {
        tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = 'csrf_token';
        form.appendChild(tokenInput);
    }
    tokenInput.value = token;
}
