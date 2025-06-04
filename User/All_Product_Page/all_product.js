function openfilter() {
    let sidebar = document.querySelector(".sidebar");


    if(sidebar.style.opacity === "0" || sidebar.style.opacity === "")
    {
        sidebar.style.opacity = "1";
        sidebar.style.visibility= "visible";
        sidebar.style.width = "250px";
        sidebar.style.padding = "10px";
    }
    else
    {
        sidebar.style.opacity = "0";
        sidebar.style.visibility = "hidden";
        sidebar.style.width = "0";
        sidebar.style.padding = "0";
    }
}

// Function to adjust cart count size based on digits
function adjustCartCountSize() {
    const cartCount = document.getElementById('cartCount');
    if (!cartCount) return;
    
    const count = cartCount.textContent.trim();
    if (count.length >= 3) {
        // For 3 or more digits (100+)
        cartCount.style.fontSize = '8px';
        cartCount.style.width = '22px';
    } else if (count.length === 2) {
        // For 2 digits (10-99)
        cartCount.style.fontSize = '10px';
        cartCount.style.width = '20px';
    } else {
        // For 1 digit (0-9)
        cartCount.style.fontSize = '12px';
        cartCount.style.width = '20px';
    }
    
    // Ensure vertical alignment
    cartCount.style.lineHeight = '1';
    cartCount.style.display = 'flex';
    cartCount.style.alignItems = 'center';
    cartCount.style.justifyContent = 'center';
}

// Function to update cart count in the header
function updateHeaderCartCount(count) {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        if (count > 0) {
            cartCount.textContent = count;
            cartCount.style.display = 'flex';
        } else {
            cartCount.textContent = '0';
            cartCount.style.display = 'none';
        }
        adjustCartCountSize();
    }
}

// Function to refresh cart count from server
function refreshHeaderCartCount() {
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
            updateHeaderCartCount(data.cart_count);
        }
    })
    .catch(error => console.error('Error refreshing cart count:', error));
}

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search); 
    const category = urlParams.get("product_categories"); 
    const brand = urlParams.get("brand");     
    const gender = urlParams.get("gender");   

    const categoryFilter = document.getElementById("category-filter");
    const genderFilter = document.getElementById("gender-filter");
    const brandFilter = document.getElementById("brand-filter");

    if (category && categoryFilter) {
        categoryFilter.style.display = "none";
    }

    if (gender && genderFilter) {
        genderFilter.style.display = "none";
    }

    if (brand && brandFilter) {
        brandFilter.style.display = "none";
    }
    
    //filter
    const checkboxes = document.querySelectorAll('.filter-checkbox');
    const minPrice = document.getElementById('minprice');
    const maxPrice = document.getElementById('maxprice');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', applyFilter);
    });

    minPrice.addEventListener('change', applyFilter);
    maxPrice.addEventListener('change', applyFilter);

    function applyFilter() {
        const selectedCategories = getCheckedValues('category');
        const selectedGenders = getCheckedValues('gender');
        const selectedBrands = getCheckedValues('brand');
        const min = minPrice.value;
        const max = maxPrice.value;
    
        const urlParams = new URLSearchParams(window.location.search); 
        const category = urlParams.get("product_categories"); 
        const gender = urlParams.get("gender");
        const brand = urlParams.get("brand");
            
        const finalCategories = selectedCategories.length > 0 ? selectedCategories : (category ? [category] : []);
        const finalGenders = selectedGenders.length > 0 ? selectedGenders : (gender ? [gender] : []);
        const finalBrands = selectedBrands.length > 0 ? selectedBrands : (brand ? [brand] : []);
    
        const formData = new FormData();
        finalCategories.forEach(cat => {
            formData.append('product_categories[]', cat);
        });
        
        // 添加性别数组
        finalGenders.forEach(g => {
            formData.append('gender[]', g);
        });
        
        // 添加品牌数组
        finalBrands.forEach(b => {
            formData.append('brand[]', b);
        });
        
        // 添加价格
        formData.append('minprice', min);
        formData.append('maxprice', max);
    
        fetch('filter_product.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(html => {
            document.querySelector('.product-container').innerHTML = html;
        });
    }
    

    function getCheckedValues(name) {
        return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`)).map(cb => cb.value);
    }
});

// Refresh cart count on page load and page show events
window.addEventListener('pageshow', function(event) {
    // If the page is shown after back button navigation (from cache)
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        refreshHeaderCartCount();
    }
});

