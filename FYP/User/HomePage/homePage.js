document.addEventListener("DOMContentLoaded", function () {
    let slideIndex = 0;
    const slides = document.querySelectorAll(".slide");
    const prevButton = document.getElementById("BillboardButton");
    const nextButton = document.getElementById("BillboardNextButton");
    let interval;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove("active");
            if (i === index) {
                slide.classList.add("active");
            }
        });
    }

    function nextSlide() {
        slideIndex = (slideIndex + 1) % slides.length;
        showSlide(slideIndex);
    }

    function prevSlide() {
        slideIndex = (slideIndex - 1 + slides.length) % slides.length;
        showSlide(slideIndex);
    }

    function startSlideshow() {
        interval = setInterval(nextSlide, 3000); 
    }

    function stopSlideshow() {
        clearInterval(interval);
    }

    prevButton.addEventListener("click", function () {
        stopSlideshow();
        prevSlide();
        startSlideshow();
    });

    nextButton.addEventListener("click", function () {
        stopSlideshow();
        nextSlide();
        startSlideshow();
    });

    showSlide(slideIndex);
    startSlideshow();
});


function newArrow() {
    let currentIndex = 0;
    const Newcolumn = document.querySelectorAll(".Newcolumn");
    const NewArrivalsContainer = document.querySelector(".NewArrivalsContainer");
    const columnWidth = Newcolumn[0].offsetWidth + 20;
    const left = document.querySelector("#prevButton");
    const right = document.querySelector("#nextButton");

    left.addEventListener("click", function () {
        if (currentIndex > 0) {
            currentIndex--;
            NewArrivalsContainer.style.transform = `translateX(-${currentIndex * columnWidth}px)`;
        }
    })

    right.addEventListener("click", function () {
        if (currentIndex < Newcolumn.length - 5) {
            currentIndex++;
            NewArrivalsContainer.style.transform = `translateX(-${currentIndex * columnWidth}px)`;
        }
    })
}

newArrow();


function promotionArrow() {
    let currentIndex = 0;
    const promotion = document.querySelectorAll(".promotion");
    const PromotionContainer = document.querySelector(".PromotionContainer");
    const columnWidth = promotion[0].offsetWidth + 20;
    const left = document.querySelector("#pButton");
    const right = document.querySelector("#pnextButton");

    left.addEventListener("click", function () {
        if (currentIndex > 0) {
            currentIndex--;
            PromotionContainer.style.transform = `translateX(-${currentIndex * columnWidth}px)`;
        }
    })

    right.addEventListener("click", function () {
        if (currentIndex < promotion.length - 5) {
            currentIndex++;
            PromotionContainer.style.transform = `translateX(-${currentIndex * columnWidth}px)`;
        }
    })
}

promotionArrow();

function jerseyArrow() {
    let currentIndex = 0;
    const jersey = document.querySelectorAll(".Jersey");
    const JerseyContainer = document.querySelector(".JerseyContainer");
    const columnWidth = jersey[0].offsetWidth + 20;
    const left = document.querySelector("#jButton");
    const right = document.querySelector("#jnextButton");

    left.addEventListener("click", function () {
        if (currentIndex > 0) {
            currentIndex--;
            JerseyContainer.style.transform = `translateX(-${currentIndex * columnWidth}px)`;
        }
    })

    right.addEventListener("click", function () {
        if (currentIndex < jersey.length - 5) {
            currentIndex++;
            JerseyContainer.style.transform = `translateX(-${currentIndex * columnWidth}px)`;
        }
    })
}

jerseyArrow();
