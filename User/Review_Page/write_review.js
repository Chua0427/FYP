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
    
    function highlightStar(value) {
        stars.forEach(star => {
            if (star.getAttribute("data-value") <= value) {
                star.classList.add("active");
            } else {
                star.classList.remove("active");
            }
        });
    }
}); 