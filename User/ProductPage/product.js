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
        mainImage.src=smallImages[3].src
    }

    
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
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', addToCart);
    }
});

function openModal()
{
    document.querySelector(".modal").style.display="flex"
}

function closeModal()
{
    document.querySelector(".modal").style.display="none"
}

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
    
    // Disable button and show loading state
    const button = document.querySelector('.add-to-cart');
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Adding...';
    
    // Send AJAX request
    fetch('/FYP/User/api/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            
            // Update cart counter if present in header
            const cartCounter = document.querySelector('.cart-counter');
            if (cartCounter && data.cart_count) {
                cartCounter.textContent = data.cart_count;
                cartCounter.style.display = 'block';
            }
        } else {
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('Error adding to cart. Please try again.', 'error');
        console.error('Add to cart error:', error);
    })
    .finally(() => {
        // Reset button state
        button.disabled = false;
        button.textContent = originalText;
    });
}

function getProductIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
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


