document.addEventListener("DOMContentLoaded", function(){
    const categoryByType = {
        "Footwear": ["Boot", "Futsal", "Running", "Court", "Training", "Football Shoes", "Kid Shoes", "School Shoes"],
        "Apparel": ["Jersey", "Jacket", "Pant", "Legging"],
        "Equipment": ["Bag", "Cap", "Football Accessories", "Sock", "Gym Accessories"]
    };

    const productType = document.getElementById("product_type");
    const productCategory = document.getElementById("product_categories");

    const existingValues = Array.from(productType.options).map(opt => opt.value);

    for (let type in categoryByType) {
        if (existingValues.includes(type)) continue; 

        let option = document.createElement("option");
        option.value = type;
        option.textContent = type;
        productType.appendChild(option);
    }

    productType.addEventListener("change", function(){
        let type = this.value;
        productCategory.innerHTML = '<option value="">Select Category</option>';

        if (type in categoryByType) {
            categoryByType[type].forEach(category => {
                let option = document.createElement("option");
                option.value = category;
                option.textContent = category;
                productCategory.appendChild(option);
            });
        }
    });

    const statusSelect = document.getElementById('status');
    const discountInput = document.getElementById('discount_price');

    function handleStatusChange() {
        const selected = statusSelect.value;

        if (selected === 'Promotion') {
            discountInput.disabled = false;
            discountInput.required = true;
            discountInput.placeholder = "Required when status is Promotion";
        } else {
            discountInput.value = ''; 
            discountInput.disabled = true;
            discountInput.required = false;
            discountInput.placeholder = "Disabled";
        }
    }

    handleStatusChange();

    statusSelect.addEventListener('change', handleStatusChange);
});
