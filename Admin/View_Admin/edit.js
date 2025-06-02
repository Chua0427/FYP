function setupPasswordToggle(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(iconId);
    
    eyeIcon.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
}

setupPasswordToggle('password', 'Password');
setupPasswordToggle('confirm_password', 'ConfirmPassword');


document.querySelector("form").addEventListener("submit", function(event) {
    let isValid = true;

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    if (password !== "") {
        const hasLowercase = /[a-z]/.test(password);
        const hasUppercase = /[A-Z]/.test(password);
        const hasDigit = /\d/.test(password);
        const hasSymbol = /[@$!%*?&.,]/.test(password);
        const isLongEnough = password.length >= 8;

        let matchCount = 0;
        if (hasLowercase) matchCount++;
        if (hasUppercase) matchCount++;
        if (hasDigit) matchCount++;
        if (hasSymbol) matchCount++;

        if (!(isLongEnough && matchCount >= 4)) {
            alert("❌ Password must be at least 8 characters and include lowercase, uppercase, number, and symbol.");
            isValid = false;
        }

        if (password !== confirmPassword) {
            document.getElementById("passwordError").style.display = "block";
            alert("❌ Confirm password does not match.");
            isValid = false;
        } else {
            document.getElementById("passwordError").style.display = "none";
        }
    }

        if (!isValid) {
            event.preventDefault();
        }
});

document.getElementById("password").addEventListener("input", function () {
    const password = this.value;

    const hasLowercase = /[a-z]/.test(password);
    const hasUppercase = /[A-Z]/.test(password);
    const hasDigit = /\d/.test(password);
    const hasSymbol = /[@$!%*?&.,]/.test(password);
    const isLongEnough = password.length >= 8;

    document.getElementById("req-length").innerText = isLongEnough ? "✅" : "❌";
    document.getElementById("req-lowercase").innerText = hasLowercase ? "✅" : "❌";
    document.getElementById("req-uppercase").innerText = hasUppercase ? "✅" : "❌";
    document.getElementById("req-digit").innerText = hasDigit ? "✅" : "❌";
    document.getElementById("req-symbol").innerText = hasSymbol ? "✅" : "❌";
});


document.getElementById("confirm_password").addEventListener("input", function(){
    const confirmPassword= this.value;
    const password= document.getElementById("password").value;
    const message= document.getElementById("passwordError");

    if (password === "") {
        message.style.display = "none"; 
        return;
    }

    if(confirmPassword !== password){
        message.style.display="block";
    }else{
        message.style.display="none";
    }
});


document.addEventListener("DOMContentLoaded", function(){
    const cityByState = {
        "Johor": ["Johor Bahru", "Batu Pahat", "Muar", "Segamat", "Kluang", "Kota Tinggi", "Pontian", "Mersing", "Tangkak"],
        "Kedah": ["Alor Setar", "Sungai Petani", "Kulim", "Langkawi", "Jitra", "Baling"],
        "Kelantan": ["Kota Bharu", "Pasir Mas", "Tanah Merah", "Gua Musang", "Tumpat"],
        "Melaka": ["Melaka City", "Ayer Keroh", "Alor Gajah", "Jasin"],
        "Negeri Sembilan": ["Seremban", "Port Dickson", "Nilai", "Bahau", "Kuala Pilah"],
        "Pahang": ["Kuantan", "Temerloh", "Bentong", "Jerantut", "Pekan", "Raub"],
        "Penang": ["George Town", "Butterworth", "Bukit Mertajam", "Bayan Lepas", "Balik Pulau"],
        "Perak": ["Ipoh", "Taiping", "Teluk Intan", "Lumut", "Sitiawan", "Kampar"],
        "Perlis": ["Kangar", "Arau", "Padang Besar"],
        "Sabah": ["Kota Kinabalu", "Sandakan", "Tawau", "Lahad Datu", "Keningau"],
        "Sarawak": ["Kuching", "Miri", "Sibu", "Bintulu", "Limbang", "Mukah"],
        "Selangor": ["Shah Alam", "Petaling Jaya", "Subang Jaya", "Klang", "Kajang", "Rawang"],
        "Terengganu": ["Kuala Terengganu", "Kemaman", "Dungun", "Marang", "Besut"],
        "Kuala Lumpur": ["Kuala Lumpur"],
        "Labuan": ["Labuan"],
        "Putrajaya": ["Putrajaya"]
    };

    const selected_state= document.getElementById("state");
    const selected_city= document.getElementById("city");

    for(let state in cityByState){
        let option=document.createElement("option");

        option.value=state;
        option.textContent=state;
        selected_state.appendChild(option);
    }

    selected_state.addEventListener("change", function(){
        const state= this.value;
        selected_city.innerHTML= '<option value="">Select City</option>'

        if(state in cityByState)
        {
            cityByState[state].forEach(city=>{
                let option=document.createElement("option");
                option.value=city;
                option.textContent=city;
                selected_city.appendChild(option);
            });
        }
    });
});

document.addEventListener("DOMContentLoaded",function(){
    const today=new Date();
    const minDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate());
    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

    const date= document.getElementById("date");
    const formatDate = (date) => date.toISOString().split("T")[0];

    date.setAttribute("min", formatDate(minDate));
    date.setAttribute("max", formatDate(maxDate));
});




