function openTab(event,tab)
{
    let content=document.querySelectorAll(".tab-content")
    content.forEach(content=> content.classList.remove("active"))

    let button=document.querySelectorAll(".tab-button")
    button.forEach(button=>button.classList.remove("active"))

    document.getElementById(tab).classList.add("active")

    event.currentTarget.classList.add("active")
}

document.addEventListener("DOMContentLoaded", function () {
    let mainImage = document.getElementById("main_image");
    let smallImages = document.querySelectorAll(".small-img");
    let currentIndex=0;

    // Handle small image clicks
    smallImages.forEach((img, index) => {
        img.onclick = function() {
            mainImage.src = img.src;
            currentIndex = index;
        }
    });
    
    // Handle navigation buttons
    document.getElementById("productButton").addEventListener("click", function () {
        currentIndex = (currentIndex - 1 + smallImages.length) % smallImages.length;
        mainImage.src = smallImages[currentIndex].src;
    });
    
    document.getElementById("productnextButton").addEventListener("click", function () {
        currentIndex = (currentIndex + 1) % smallImages.length;
        mainImage.src = smallImages[currentIndex].src;
    });

    // Add to Cart Button functionality
    const addToCartBtn = document.querySelector('.add-to-cart');
    if (addToCartBtn && !addToCartBtn.getAttribute('data-requires-auth')) {
        addToCartBtn.addEventListener('click', addToCart);
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
});

function addToCart() {
    // Get product details from the page
    const productId = getProductIdFromUrl();
    const sizeSelect = document.getElementById('size');
    const quantityInput = document.getElementById('quantity');
    
    // Validate selection
    if (!sizeSelect || sizeSelect.options.length === 0) {
        showMessage('Please select a size', 'error');
        return;
    }
    
    const size = sizeSelect.value;
    const quantity = parseInt(quantityInput.value, 10) || 1;
    
    if (quantity <= 0) {
        showMessage('Please select a valid quantity', 'error');
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
    
    // Disable button and show loading state
    const button = document.querySelector('.add-to-cart');
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Adding...';
    
    // Send AJAX request
    fetch('/FYP/FYP/User/api/add_to_cart.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin' // Include cookies for authentication
    })
    .then(response => {
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
            
            // Update cart counter if present in header
            const cartCounter = document.querySelector('.cart-counter');
            if (cartCounter && data.cart_count) {
                cartCounter.textContent = data.cart_count;
                cartCounter.style.display = 'block';
            }
            
            // Reset quantity to 1 after successful add
            if (quantityInput) {
                quantityInput.value = 1;
            }
        } else {
            showMessage(data.error || 'Unknown error occurred', 'error');
        }
    })
    .catch(error => {
        showMessage(error.message || 'Error adding to cart. Please try again.', 'error');
        console.error('Add to cart error:', error);
    })
    .finally(() => {
        // Reset button state
        button.disabled = false;
        button.textContent = originalText;
        
        // Retry in case of network errors
        if (navigator.onLine === false) {
            // Wait for online status and retry
            window.addEventListener('online', function onlineHandler() {
                window.removeEventListener('online', onlineHandler);
                showMessage('Connection restored. Retrying...', 'info');
                setTimeout(addToCart, 1000);
            });
        }
    });
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
        
        // Style the container
        messageContainer.style.position = 'fixed';
        messageContainer.style.top = '20px';
        messageContainer.style.right = '20px';
        messageContainer.style.zIndex = '1000';
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
    
    // Remove after 3 seconds
    setTimeout(() => {
        messageElement.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            messageElement.remove();
        }, 300);
    }, 3000);
}

function openModal()
{
    document.querySelector(".modal").style.display="flex"
}

function closeModal()
{
    document.querySelector(".modal").style.display="none"
}

// Add keyframe animations to document
const style = document.createElement('style');
style.textContent = `
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-10px); }
}
`;
document.head.appendChild(style);


