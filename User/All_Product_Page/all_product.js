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
});

