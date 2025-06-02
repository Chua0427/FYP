/**
 * VeroSports Shopping Cart JavaScript
 * Handles all cart interactions including quantity updates and item removal
 */

// Global cache to prevent redundant operations
const cartOperationsCache = {
    lastUpdate: null,
    pendingUpdates: {},
    updateTimer: null
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all cart functionality
    initCartFunctions();

    // Get CSRF token
    const csrfToken = getCsrfToken();
    
    // Use event delegation for cart interactions
    const cartContainer = document.querySelector('.cart-container');
    if (cartContainer) {
        // Handle all quantity changes through event delegation
        cartContainer.addEventListener('click', function(e) {
            // Handle quantity minus button
            if (e.target.classList.contains('quantity-btn') && e.target.classList.contains('minus')) {
                const cartId = e.target.getAttribute('data-cart-id');
                const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
                let value = parseInt(input.value, 10);
                value = Math.max(1, value - 1);
                input.value = value;
                
                // Update quantity via AJAX
                updateQuantity(cartId, value, csrfToken);
                animateButton(e.target, 'minus');
            }
            
            // Handle quantity plus button
            else if (e.target.classList.contains('quantity-btn') && e.target.classList.contains('plus')) {
                const cartId = e.target.getAttribute('data-cart-id');
                const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
                let value = parseInt(input.value, 10);
                value += 1;
                input.value = value;
                
                // Update quantity via AJAX
                updateQuantity(cartId, value, csrfToken);
                animateButton(e.target, 'plus');
            }
            
            // Handle remove button
            else if (e.target.classList.contains('remove-btn')) {
                if (confirm('Are you sure you want to remove this item?')) {
                    const cartId = e.target.getAttribute('data-cart-id');
                    
                    // Find the product element with error handling
                    let productElement = null;
                    try {
                        const selector = document.querySelector(`.product-select[value="${cartId}"]`);
                        if (selector) {
                            productElement = selector.closest('.product');
                        } else {
                            // Fallback to the button's parent if product-select isn't found
                            productElement = e.target.closest('.product');
                        }
                    } catch (error) {
                        // Fallback to the button's parent if selector fails
                        productElement = e.target.closest('.product');
                        console.error('Error finding product element:', error);
                    }
                    
                    // Add visual indication before removing
                    if (productElement) {
                        productElement.style.opacity = '0.5';
                        productElement.style.transition = 'opacity 0.3s';
                    }
                    
                    // Create form data
                    const formData = new FormData();
                    formData.append('csrf_token', csrfToken);
                    formData.append('cart_id', cartId);
                    formData.append('ajax', '1');
                    
                    console.log('Attempting to remove cart item:', cartId);
                    
                    // Send AJAX request
                    fetch('remove_cart_item.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Remove cart item response status:', response.status);
                        if (!response.ok) {
                            if (response.status === 401) {
                                // Redirect to login if unauthorized
                                window.location.href = '../login/login.php?redirect=' + encodeURIComponent(window.location.href);
                                throw new Error('Please login to update your cart');
                            }
                            // First try to parse as JSON - most server errors will be in JSON format
                            return response.text().then(text => {
                                try {
                                    const data = JSON.parse(text);
                                    // If we successfully parsed JSON, it's a structured error from the server
                                    throw new Error(data.error || 'Error removing item');
                                } catch (jsonError) {
                                    // Only if JSON parsing failed, treat it as a non-JSON response
                                    if (jsonError instanceof SyntaxError) {
                                        console.error('Server returned non-JSON response:', text.substring(0, 100));
                                        throw new Error('Error communicating with server. Please refresh and try again.');
                                    } else {
                                        // If it's not a syntax error but our own thrown error, pass it through
                                        throw jsonError;
                                    }
                                }
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove the item from the DOM
                            if (productElement) {
                                productElement.remove();
                            }
                            
                            // Update cart count in header
                            updateCartCounters(data.cart_count);
                            
                            // Update cart totals
                            updateCartTotals();
                            
                            // If no more items in cart, reload page to show empty cart message
                            const remainingItems = document.querySelectorAll('.product').length;
                            if (remainingItems === 0) {
                                location.reload();
                            }
                        } else {
                            // Reset opacity if error
                            if (productElement) {
                                productElement.style.opacity = '1';
                            }
                            alert(data.error || 'An error occurred while removing the item.');
                        }
                    })
                    .catch(error => {
                        // Reset opacity if error
                        if (productElement) {
                            productElement.style.opacity = '1';
                        }
                        console.error('Remove item error:', error);

                        // Show user-friendly message based on error
                        let userMessage = 'Error removing item. Please try again.';
                        
                        // Check for common error patterns
                        if (error.message && error.message.includes('already deleted')) {
                            userMessage = 'This item may have been already removed from your cart. Refreshing page...';
                            setTimeout(() => location.reload(), 1500);
                        } else if (error.message && error.message.includes('Database connection')) {
                            userMessage = 'Database connection issue. Please try again in a moment.';
                        } else if (error.message && error.message.includes('non-JSON')) {
                            userMessage = 'Communication error with server. Refreshing page...';
                            setTimeout(() => location.reload(), 1500);
                        }
                        
                        alert(userMessage);
                    });
                }
            }
        });
        
        // Handle input field changes with debounce
        cartContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                const cartId = e.target.name.match(/\[(\d+)\]/)[1];
                let value = parseInt(e.target.value, 10);
                value = Math.max(1, value); // Ensure minimum value is 1
                e.target.value = value;
                
                // Update quantity via AJAX with debounce
                debounce(() => {
                    updateQuantity(cartId, value, csrfToken);
                }, 300)();
            }
        });
    } else {
        // Fall back to direct element binding if container isn't present
        bindIndividualCartElements(csrfToken);
    }
    
    // Add event listener to store checkboxes
    initStoreCheckboxes();
    
    // Listen for cart updates from other pages (like product.js)
    document.addEventListener('cartUpdated', function(e) {
        if (e.detail && e.detail.cartCount) {
            // Update any cart count indicators
            updateCartCounters(e.detail.cartCount);
        }
    });
});

/**
 * Fall back to individual element binding when container isn't found
 */
function bindIndividualCartElements(csrfToken) {
    const minusButtons = document.querySelectorAll('.quantity-btn.minus');
    const plusButtons = document.querySelectorAll('.quantity-btn.plus');
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
            updateQuantity(cartId, value, csrfToken);
            animateButton(this, 'minus');
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
            updateQuantity(cartId, value, csrfToken);
            animateButton(this, 'plus');
        });
    });
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.name.match(/\[(\d+)\]/)[1];
            let value = parseInt(this.value, 10);
            value = Math.max(1, value); // Ensure minimum value is 1
            this.value = value;
            
            // Update quantity via AJAX with debounce
            debounce(() => {
                updateQuantity(cartId, value, csrfToken);
            }, 300)();
        });
    });
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item?')) {
                const cartId = this.getAttribute('data-cart-id');
                
                // Find the product element with error handling
                let productElement = null;
                try {
                    const selector = document.querySelector(`.product-select[value="${cartId}"]`);
                    if (selector) {
                        productElement = selector.closest('.product');
                    } else {
                        // Fallback to the button's parent if product-select isn't found
                        productElement = this.closest('.product');
                    }
                } catch (error) {
                    // Fallback to the button's parent if selector fails
                    productElement = this.closest('.product');
                    console.error('Error finding product element:', error);
                }
                
                // Add visual indication before removing
                if (productElement) {
                    productElement.style.opacity = '0.5';
                    productElement.style.transition = 'opacity 0.3s';
                }
                
                // Create form data
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('cart_id', cartId);
                formData.append('ajax', '1');
                
                console.log('Attempting to remove cart item:', cartId);
                
                // Send AJAX request
                fetch('remove_cart_item.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Remove cart item response status:', response.status);
                    if (!response.ok) {
                        if (response.status === 401) {
                            // Redirect to login if unauthorized
                            window.location.href = '../login/login.php?redirect=' + encodeURIComponent(window.location.href);
                            throw new Error('Please login to update your cart');
                        }
                        // First try to parse as JSON - most server errors will be in JSON format
                        return response.text().then(text => {
                            try {
                                const data = JSON.parse(text);
                                // If we successfully parsed JSON, it's a structured error from the server
                                throw new Error(data.error || 'Error removing item');
                            } catch (jsonError) {
                                // Only if JSON parsing failed, treat it as a non-JSON response
                                if (jsonError instanceof SyntaxError) {
                                    console.error('Server returned non-JSON response:', text.substring(0, 100));
                                    throw new Error('Error communicating with server. Please refresh and try again.');
                                } else {
                                    // If it's not a syntax error but our own thrown error, pass it through
                                    throw jsonError;
                                }
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove the item from the DOM
                        if (productElement) {
                            productElement.remove();
                        }
                        
                        // Update cart count in header
                        updateCartCounters(data.cart_count);
                        
                        // Update cart totals
                        updateCartTotals();
                        
                        // If no more items in cart, reload page to show empty cart message
                        const remainingItems = document.querySelectorAll('.product').length;
                        if (remainingItems === 0) {
                            location.reload();
                        }
                    } else {
                        // Reset opacity if error
                        if (productElement) {
                            productElement.style.opacity = '1';
                        }
                        alert(data.error || 'An error occurred while removing the item.');
                    }
                })
                .catch(error => {
                    // Reset opacity if error
                    if (productElement) {
                        productElement.style.opacity = '1';
                    }
                    console.error('Remove item error:', error);

                    // Show user-friendly message based on error
                    let userMessage = 'Error removing item. Please try again.';
                    
                    // Check for common error patterns
                    if (error.message && error.message.includes('already deleted')) {
                        userMessage = 'This item may have been already removed from your cart. Refreshing page...';
                        setTimeout(() => location.reload(), 1500);
                    } else if (error.message && error.message.includes('Database connection')) {
                        userMessage = 'Database connection issue. Please try again in a moment.';
                    } else if (error.message && error.message.includes('non-JSON')) {
                        userMessage = 'Communication error with server. Refreshing page...';
                        setTimeout(() => location.reload(), 1500);
                    }
                    
                    alert(userMessage);
                });
            }
        });
    });
}

/**
 * Initialize all cart functions
 */
function initCartFunctions() {
    // Handle checkout button animation
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        // Use CSS transform instead of JS for better performance
        checkoutBtn.style.transition = 'transform 0.1s ease-in-out';
        
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
    
    // Update totals when page loads
    updateCartTotals();
}

/**
 * Initialize store checkboxes to select/deselect all items in a store
 * using event delegation for better performance
 */
function initStoreCheckboxes() {
    const cartContent = document.querySelector('.cart-content');
    if (!cartContent) return;
    
    cartContent.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox' && e.target.closest('.store h3')) {
            const storeDiv = e.target.closest('.store');
            const isChecked = e.target.checked;
            
            // Update visual feedback when toggling store checkbox
            storeDiv.style.opacity = isChecked ? '1' : '0.6';
            
            // Get all product checkboxes in this store
            const productCheckboxes = storeDiv.querySelectorAll('.product input[type="checkbox"]');
            productCheckboxes.forEach(productCheckbox => {
                productCheckbox.checked = isChecked;
            });
            
            // Update order totals
            updateCartTotals();
        }
        else if (e.target.type === 'checkbox' && e.target.closest('.product')) {
            // Individual product checkbox changed, update totals
            updateCartTotals();
        }
    });
}

/**
 * Debounce function to prevent excessive callbacks
 */
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(context, args);
        }, wait);
    };
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
 * Update quantity in cart via AJAX
 */
function updateQuantity(cartId, quantity, csrfToken) {
    // Skip unnecessary updates
    if (cartOperationsCache.pendingUpdates[cartId] === quantity) {
        return;
    }
    
    // Visual feedback
    const input = document.querySelector(`input[name="quantity[${cartId}]"]`);
    if (input) {
        input.style.opacity = '0.7';
        input.style.pointerEvents = 'none';
    }
    
    // Queue update
    cartOperationsCache.pendingUpdates[cartId] = quantity;
    
    // Create form data
    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);
    formData.append('ajax', '1');
    
    // Minimal UI Blocking during fetch
    fetch('update_cart.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Remove from pending updates
        delete cartOperationsCache.pendingUpdates[cartId];
        
        // Reset visual feedback
        if (input) {
            input.style.opacity = '1';
            input.style.pointerEvents = 'auto';
        }
        
        if (!response.ok) {
            if (response.status === 401) {
                // Redirect to login if unauthorized
                window.location.href = '../login/login.php?redirect=' + encodeURIComponent(window.location.href);
                throw new Error('Please login to update your cart');
            }
            // First try to parse as JSON - most server errors will be in JSON format
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    // If we successfully parsed JSON, it's a structured error from the server
                    throw new Error(data.error || 'Error updating cart');
                } catch (jsonError) {
                    // Only if JSON parsing failed, treat it as a non-JSON response
                    if (jsonError instanceof SyntaxError) {
                        console.error('Server returned non-JSON response:', text.substring(0, 100));
                        throw new Error('Error communicating with server. Please refresh and try again.');
                    } else {
                        // If it's not a syntax error but our own thrown error, pass it through
                        throw jsonError;
                    }
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            alert(data.error || 'An error occurred while updating your cart.');
            // Reset to original value
            if (input) {
                // Try to get the original value from the server response or use the current value
                const originalQuantity = data.item ? data.item.quantity : input.defaultValue;
                input.value = originalQuantity || quantity;
            }
        } else {
            // Update item total price if available
            const itemPriceEl = document.querySelector(`[data-cart-id="${cartId}"] .item-price`);
            if (itemPriceEl && data.item && data.item.total) {
                itemPriceEl.textContent = formatCurrency(data.item.total);
            }
            
            // Update cart totals
            updateCartTotals();
        }
    })
    .catch(error => {
        console.error('Update cart error:', error);
        // Reset visual feedback
        if (input) {
            input.style.opacity = '1';
            input.style.pointerEvents = 'auto';
        }
        alert(error.message || 'Error updating your cart. Please try again.');
    });
}

/**
 * Update cart totals based on selected items
 */
function updateCartTotals() {
    // Find all selected product checkboxes
    const selectedItems = document.querySelectorAll('.product input[type="checkbox"]:checked');
    let subtotal = 0;
    let itemCount = 0;
    
    // Calculate subtotal based on selected items
    selectedItems.forEach(checkbox => {
        const priceElement = checkbox.closest('.product').querySelector('.price');
        const quantityElement = checkbox.closest('.product').querySelector('.quantity-input');
        
        if (priceElement && quantityElement) {
            const price = parseFloat(priceElement.getAttribute('data-price') || priceElement.textContent.replace(/[^\d.]/g, ''));
            const quantity = parseInt(quantityElement.value, 10);
            
            if (!isNaN(price) && !isNaN(quantity)) {
                subtotal += price * quantity;
                itemCount += quantity;
            }
        }
    });
    
    // Update subtotal display
    const subtotalElement = document.querySelector('.subtotal-amount');
    if (subtotalElement) {
        subtotalElement.textContent = formatCurrency(subtotal);
    }
    
    // Update item count if element exists
    const itemCountElement = document.querySelector('.item-count');
    if (itemCountElement) {
        itemCountElement.textContent = itemCount;
    }
}

/**
 * Format number as currency
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MYR'
    }).format(amount);
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
 * Update all cart count indicators on the page
 * @param {number} count - New cart count
 */
function updateCartCounters(count) {
    const counters = document.querySelectorAll('.cart-counter');
    counters.forEach(counter => {
        counter.textContent = count;
        counter.style.display = count > 0 ? 'block' : 'none';
    });
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

// Refresh cart count on back/forward navigation
window.addEventListener('pageshow', function(event) {
    // If the page is shown after back button navigation (from cache)
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        // Refresh cart count with the latest data from server
        refreshCartCount();
    }
});

// Function to refresh cart count
function refreshCartCount() {
    fetch('/FYP/FYP/User/api/get_cart_count.php', {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart counter in header
            const cartCount = document.getElementById('cartCount');
            if (cartCount) {
                const count = data.cart_count;
                if (count > 0) {
                    cartCount.textContent = count;
                    cartCount.style.display = 'flex';
                    // Add pulse animation
                    cartCount.classList.add('pulse');
                    setTimeout(() => {
                        cartCount.classList.remove('pulse');
                    }, 500);
                } else {
                    cartCount.style.display = 'none';
                }
            }
        }
    })
    .catch(error => console.error('Error refreshing cart count:', error));
}
