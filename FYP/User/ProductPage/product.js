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
});
