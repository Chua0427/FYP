document.addEventListener('DOMContentLoaded', function() {
    
    let currentStatus = "shipping"; 
    
    let steps = document.querySelectorAll('.step');
    
    const statusOrder = ["preparing", "packing", "assign", "shipping", "delivered"];
    
    let currentIndex = statusOrder.indexOf(currentStatus);
    
    steps.forEach((step, index) => {
        if (index <= currentIndex) {
            step.classList.add('completed');
        }
    });
});