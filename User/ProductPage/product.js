function openTab(event,tab)
{
    let content=document.querySelectorAll(".tab-content")
    content.forEach(content=> content.classList.remove("active"))

    let button=document.querySelectorAll(".tab-button")
    button.forEach(button=>button.classList.remove("active"))

    document.getElementById(tab).classList.add("active")

    event.currentTarget.classList.add("active")
}

// Global cache for cart operations
const cartCache = {
    isAddingToCart: false,
    lastAddedProduct: null,
    requestsInProgress: 0
};

document.addEventListener("DOMContentLoaded", function () {
    let mainImage = document.getElementById("main_image");
    let smallImages = document.querySelectorAll(".small-img");
    let currentIndex=0;


    smallImages[0].onclick = function(){
        mainImage.src=smallImages[0].src
    }

    smallImages[1].onclick = function(){
        mainImage.src=smallImages[1].src
    }

    smallImages[2].onclick = function(){
        mainImage.src=smallImages[2].src
    }
    
    smallImages[3].onclick = function(){
        mainImage.src = smallImages[3].src;
    };

    // Handle navigation buttons
    const prevButton = document.getElementById("productButton");
    const nextButton = document.getElementById("productnextButton");
    
    if (prevButton) {
        prevButton.addEventListener("click", function () {
            currentIndex = (currentIndex - 1 + smallImages.length) % smallImages.length;
            mainImage.src = smallImages[currentIndex].src;
        });
    }
    
    if (nextButton) {
        nextButton.addEventListener("click", function () {
            currentIndex = (currentIndex + 1) % smallImages.length;
            mainImage.src = smallImages[currentIndex].src;
        });
    }

    // Add to Cart Button functionality - with debounce protection
    const addToCartBtn = document.querySelector('.add-to-cart');
    if (addToCartBtn && !addToCartBtn.getAttribute('data-requires-auth')) {
        // Use debounced version of addToCart
        addToCartBtn.addEventListener('click', debounce(addToCart, 300));
    }
    
    // Add quantity validation
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            const value = parseInt(this.value, 10);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            }
        });
    }

    // Preload product images for smoother experience
    if (smallImages && smallImages.length > 0) {
        preloadImages(Array.from(smallImages).map(img => img.src));
    }

    // Prefetch cart API to reduce latency on first call
    prefetchResource('/FYP/FYP/User/api/add_to_cart.php');
});

function openModal()
{
    document.querySelector(".modal").style.display="flex"
}

function closeModal()
{
    document.querySelector(".modal").style.display="none"
}

// Debounce function to prevent multiple rapid clicks
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

function addToCart() {
    // Prevent multiple simultaneous requests
    if (cartCache.isAddingToCart) {
        showMessage('Request already in progress', 'info');
        return;
    }
    
    cartCache.isAddingToCart = true;
    
    // Get product details from the page
    const productId = getProductIdFromUrl();
    const sizeSelect = document.getElementById('size');
    const quantityInput = document.getElementById('quantity');
    
    // Validate selection
    if (!sizeSelect || sizeSelect.options.length === 0) {
        showMessage('Please select a size', 'error');
        cartCache.isAddingToCart = false;
        return;
    }
    
    const size = sizeSelect.value;
    const quantity = parseInt(quantityInput.value, 10) || 1;
    
    if (quantity <= 0) {
        showMessage('Please select a valid quantity', 'error');
        cartCache.isAddingToCart = false;
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_size', size);
    formData.append('quantity', quantity);
    
    // Add CSRF token if available
    const csrfToken = getCsrfToken();
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }
    
    // Create a request ID for better tracking
    const requestId = Date.now().toString();
    formData.append('request_id', requestId);
    
    // Disable button and show loading state
    const button = document.querySelector('.add-to-cart');
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Adding...';
    
    // Track this request
    cartCache.requestsInProgress++;
    
    // Use AbortController for request timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 8000); // 8 second timeout
    
    // Send AJAX request
    fetch('/FYP/FYP/User/api/add_to_cart.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin', // Include cookies for authentication
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        
        if (!response.ok) {
            if (response.status === 401) {
                // Redirect to login if unauthorized
                window.location.href = '/FYP/User/login/login.php?redirect=' + encodeURIComponent(window.location.href);
                throw new Error('Please login to add items to your cart');
            } else if (response.status === 403) {
                // Handle CSRF token errors by refreshing the page
                window.location.reload();
                throw new Error('Session expired. Please try again.');
            }
            return response.json().then(err => {
                throw new Error(err.error || 'Error processing request');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            
            // Cache the successful response
            cartCache.lastAddedProduct = {
                id: productId,
                size: size,
                quantity: quantity,
                timestamp: Date.now()
            };
            
            // Update cart counter if present in header
            updateCartCounter(data.cart_count);
            
            // Reset quantity to 1 after successful add
            if (quantityInput) {
                quantityInput.value = 1;
            }
            
            // Trigger custom event that other components might listen for
            document.dispatchEvent(new CustomEvent('cartUpdated', { 
                detail: { cartCount: data.cart_count }
            }));
        } else {
            showMessage(data.error || 'Unknown error occurred', 'error');
        }
    })
    .catch(error => {
        if (error.name === 'AbortError') {
            showMessage('Request timed out. Please try again.', 'error');
        } else {
            showMessage(error.message || 'Error adding to cart. Please try again.', 'error');
            console.error('Add to cart error:', error);
        }
    })
    .finally(() => {
        // Reset button state
        button.disabled = false;
        button.textContent = originalText;
        cartCache.isAddingToCart = false;
        
        // Decrement pending requests counter
        cartCache.requestsInProgress--;
        
        // Retry in case of network errors
        if (navigator.onLine === false) {
            // Wait for online status and retry
            window.addEventListener('online', function onlineHandler() {
                window.removeEventListener('online', onlineHandler);
                showMessage('Connection restored. Retrying...', 'info');
                setTimeout(() => {
                    if (!cartCache.isAddingToCart) {
                        addToCart();
                    }
                }, 1000);
            });
        }
    });
}

// Update cart counter with animation
function updateCartCounter(count) {
    const cartCounter = document.querySelector('.cart-counter');
    if (!cartCounter) return;
    
    if (count) {
        // Create animation effect
        cartCounter.classList.add('pulse');
        cartCounter.textContent = count;
        cartCounter.style.display = 'block';
        
        // Remove animation class after animation completes
        setTimeout(() => {
            cartCounter.classList.remove('pulse');
        }, 500);
    } else {
        cartCounter.style.display = 'none';
    }
}

function getProductIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

/**
 * Get CSRF token from the page or meta tag
 * @returns {string|null} CSRF token or null if not available
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

function showMessage(message, type) {
    // Check if a message container already exists
    let messageContainer = document.querySelector('.message-container');
    
    // If not, create one
    if (!messageContainer) {
        messageContainer = document.createElement('div');
        messageContainer.className = 'message-container';
        document.body.appendChild(messageContainer);
        
        // Style the container to appear below the cart icon
        messageContainer.style.position = 'fixed';
        messageContainer.style.top = '70px';
        messageContainer.style.right = '20px';
        messageContainer.style.left = 'auto';
        messageContainer.style.width = '300px';
        messageContainer.style.zIndex = '999';
        messageContainer.style.textAlign = 'center';
    }
    
    // Create message element
    const messageElement = document.createElement('div');
    messageElement.className = `message ${type}`;
    messageElement.innerHTML = message;
    
    // Style the message
    messageElement.style.padding = '12px 20px';
    messageElement.style.marginBottom = '10px';
    messageElement.style.borderRadius = '4px';
    messageElement.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
    messageElement.style.animation = 'fadeIn 0.3s ease-out';
    messageElement.style.display = 'block';
    messageElement.style.width = '100%';
    messageElement.style.boxSizing = 'border-box';
    
    // Set colors based on message type
    if (type === 'success') {
        messageElement.style.backgroundColor = '#28a745';
        messageElement.style.color = 'white';
    } else if (type === 'error') {
        messageElement.style.backgroundColor = '#dc3545';
        messageElement.style.color = 'white';
    } else if (type === 'info') {
        messageElement.style.backgroundColor = '#17a2b8';
        messageElement.style.color = 'white';
    }
    
    // Add to container
    messageContainer.appendChild(messageElement);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        messageElement.style.opacity = '0';
        messageElement.style.transition = 'opacity 0.5s ease';
        
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
            
            // Remove container if empty
            if (messageContainer.children.length === 0) {
                document.body.removeChild(messageContainer);
            }
        }, 500);
    }, 3000);
}

// Preload images for better user experience
function preloadImages(urls) {
    if (!urls || urls.length === 0) return;
    
    urls.forEach(url => {
        const img = new Image();
        img.src = url;
    });
}

// Prefetch resources to reduce latency
function prefetchResource(url) {
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = url;
    document.head.appendChild(link);
}


