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

document.addEventListener("DOMContentLoaded", function () {
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
        
        // add gender array
        finalGenders.forEach(g => {
            formData.append('gender[]', g);
        });
        
        //  add brand array
        finalBrands.forEach(b => {
            formData.append('brand[]', b);
        });
        
        // add price
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
