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
    
    // Handle browser back navigation if review already submitted
    if (sessionStorage.getItem('reviewSubmitted') === 'true') {
        window.addEventListener('pageshow', function(event) {
            // event.persisted is true on Safari back-forward navigation
            // performance.navigation.type === 2 indicates back/forward navigation in other browsers
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                showReviewSubmittedAlert();
            }
        });
    }
    
    // Add event listener to form submit
    const reviewForm = document.querySelector('form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            if (validateForm(e)) {
                sessionStorage.setItem('reviewSubmitted', 'true');
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
    
    function showReviewSubmittedAlert() {
        alert('You have already submitted a review for this product');
        window.location.href = '../order/orderhistory.php';
    }
}); 