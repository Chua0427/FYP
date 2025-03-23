document.addEventListener("DOMContentLoaded",function(){
    let star= document.querySelectorAll(".star");
    let rating= document.getElementById("ratingValue");

    star.forEach(star=>{
        star.addEventListener("click",function(){
            let value = this.getAttribute("data-value");
            rating.value=value;
            highlightStar(value);
        })
    })

    function highlightStar(value){
        star.forEach(star=>{
            if(star.getAttribute("data-value")<=value){
                star.classList.add("active");
            }
            else{
                star.classList.remove("active");
            }
        })
    }

})
