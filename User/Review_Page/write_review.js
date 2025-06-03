document.addEventListener("DOMContentLoaded", function() {
    // Ensure currentReviewKey is defined
    if (typeof currentReviewKey === 'undefined') {
        console.error('currentReviewKey is not defined');
        return;
    }
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
                // Set flag to indicate successful form submission for this product
                try {
                    sessionStorage.setItem(currentReviewKey, 'submitted');
                } catch(e) {
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
    
    // Clear only this page's review flag
    function clearReviewFlag() {
        try {
            sessionStorage.removeItem(currentReviewKey);
        } catch(e) {
            console.log('SessionStorage not available');
        }
    }
    
    // Handle browser back/forward navigation
    window.addEventListener('pageshow', function(event) {
        // If this is a back/forward navigation (page from cache)
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            checkForReviewSubmission();
        }
    });
    
    // Check if user is trying to access review page after submitting
    function checkForReviewSubmission() {
        try {
            // Check this page's review flag
            const flag = sessionStorage.getItem(currentReviewKey);
            if (flag === 'submitted' || flag === 'already') {
                // Clear flag immediately
                sessionStorage.removeItem(currentReviewKey);
                
                alert('You have already submitted a review for this product');
                    window.location.href = '../order/orderhistory.php';
                return;
            }
        } catch(e) {
            console.log('SessionStorage not available');
        }
    }
    
    // Clean up on page unload to prevent stale flags
    window.addEventListener('beforeunload', function() {
        // Only clear flags if we're not in the middle of form submission
        clearReviewFlag();
    });
    
    // Initialize page - check if user shouldn't be here
    checkForReviewSubmission();
});