document.addEventListener("DOMContentLoaded", function() {
    let stars = document.querySelectorAll(".star");
    let rating = document.getElementById("ratingValue");
    
    // Check if there's an initial rating (for editing existing reviews)
    if (rating.value > 0) {
        highlightStar(rating.value);
    }
    
    stars.forEach(star => {
        star.addEventListener("click", function() {
            let value = this.getAttribute("data-value");
            rating.value = value;
            highlightStar(value);
        });
    });
    
    // Highlight stars on hover
    stars.forEach(star => {
        star.addEventListener("mouseover", function() {
            highlightStar(this.getAttribute("data-value"));
        });
    });
    
    // Reset stars when not hovering
    const starContainer = document.querySelector('.star-rating');
    if (starContainer) {
        starContainer.addEventListener("mouseleave", function() {
            highlightStar(rating.value);
        });
    }
    
    function highlightStar(value) {
        stars.forEach(star => {
            if (star.getAttribute("data-value") <= value) {
                star.classList.add("active");
            } else {
                star.classList.remove("active");
            }
        });
    }
    
    // Add event listener to form submit
    const reviewForm = document.querySelector('form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            if (validateForm(e)) {
                // Clear any existing navigation flags before submitting
                clearNavigationFlags();
                // Set flag to indicate successful form submission
                try {
                    sessionStorage.setItem('reviewSubmitted', 'true');
                } catch(e) {
                    // Handle case where sessionStorage is not available
                    console.log('SessionStorage not available');
                }
            }
        });
    }
    
    function validateForm(e) {
        const ratingValue = document.getElementById('ratingValue').value;
        const reviewText = document.getElementById('review').value.trim();
        
        if (ratingValue < 1 || ratingValue > 5) {
            alert('Please select a rating from 1 to 5 stars');
            e.preventDefault();
            return false;
        }
        
        if (reviewText === '') {
            alert('Please enter your review text');
            e.preventDefault();
            return false;
        }
        
        return true;
    }
    
    // Clear all navigation-related flags
    function clearNavigationFlags() {
        try {
            sessionStorage.removeItem('formSubmitting');
            sessionStorage.removeItem('reviewSubmitted');
        } catch(e) {
            console.log('SessionStorage not available');
        }
    }
    
    // Handle browser back/forward navigation - simplified approach
    window.addEventListener('pageshow', function(event) {
        // If this is a back/forward navigation (page from cache)
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            checkForReviewSubmission();
        }
    });
    
    // Check if user is trying to access review page after submitting
    function checkForReviewSubmission() {
        try {
            const reviewSubmitted = sessionStorage.getItem('reviewSubmitted');
            if (reviewSubmitted === 'true') {
                // Clear the flag immediately to prevent repeated alerts
                sessionStorage.removeItem('reviewSubmitted');
                
                // Show alert and redirect
                alert('You have already submitted a review for this product');
                
                // Redirect to order history after a brief delay
                setTimeout(function() {
                    window.location.href = '../order/orderhistory.php';
                }, 100);
            }
        } catch(e) {
            console.log('SessionStorage not available');
        }
    }
    
    // Clean up on page unload to prevent stale flags
    window.addEventListener('beforeunload', function() {
        // Only clear flags if we're not in the middle of form submission
        const currentUrl = window.location.pathname;
        if (currentUrl.includes('write_review') || currentUrl.includes('review')) {
            try {
                // Don't clear reviewSubmitted flag if form was just submitted
                const formElements = document.querySelector('form');
                if (formElements && !document.activeElement.closest('form')) {
                    sessionStorage.removeItem('formSubmitting');
                }
            } catch(e) {
                console.log('SessionStorage not available');
            }
        }
    });
    
    // Initialize page - check if user shouldn't be here
    checkForReviewSubmission();
});