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


document.getElementById("email").addEventListener("input", function() {
    const emailInput = this.value;
    const emailError = document.getElementById("emailError");

    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (emailPattern.test(emailInput)) {
        emailError.style.display = "none"; 
    } else {
        emailError.style.display = "block"; 
    }
});


document.getElementById("mobile_number").addEventListener("input", function(){
    const telNum= this.value;
    const numberError= document.getElementById("telError");

    const telPattern= /^\+?(01[0-9]-?\d{7,8})$/;

    if(telPattern.test(telNum)){
        numberError.style.display="none";
    }else{
        numberError.style.display="block";
    }
});

document.getElementById("password").addEventListener("input", function(){
    const password=this.value;
    const strength= document.getElementById("strength");
    const submitBtn= document.getElementById("submit");

    const hasLowercase = /[a-z]/.test(password);
    const hasUppercase = /[A-Z]/.test(password);
    const hasDigit = /\d/.test(password);
    const hasSymbol = /[@$!%*?&.,]/.test(password);

    let matchCount = 0;
    if (hasLowercase) matchCount++;
    if (hasUppercase) matchCount++;
    if (hasDigit) matchCount++;
    if (hasSymbol) matchCount++;

    if (password.length < 8) {
        strength.innerText = "❗ At Least 8 Characters";
        strength.style.color = "gray";
        submitBtn.disabled = true;
    } else if (matchCount === 1) {
        strength.innerText = "🔴🔴🔴 Weak Password";
        strength.style.color = "red";
        submitBtn.disabled = true;
    } else if (matchCount === 2) {
        strength.innerText = "🟠🟠🟠 Medium Password";
        strength.style.color = "orange";
        submitBtn.disabled = true;
    } else if (matchCount === 3) {
        strength.innerText = "🟢🟢🟢 Strong Password";
        strength.style.color = "green";
        submitBtn.disabled = true;
    } else if (matchCount === 4) {
        strength.innerText = "🟢🟢🟢 Very Strong Password";
        strength.style.color = "darkgreen";
        submitBtn.disabled = false;
    }
    
});

document.getElementById("confirm_password").addEventListener("input", function(){
    const confirmPassword= this.value;
    const password= document.getElementById("password").value;
    const submitBtn= document.getElementById("submit");

    const message= document.getElementById("passwordError");

    if(confirmPassword !== password){
        message.style.display="block";
        submitBtn.disabled = true;
    }else{
        message.style.display="none";
        submitBtn.disabled = false;
    }
});

document.getElementById("postcode").addEventListener("input", function(){
    const postcode=this.value;
    const error=document.getElementById("postcodeError");

    const postcodePattern= /^[0-9]{5}$/;

    if(postcodePattern.test(postcode)){
        error.style.display= "none"
    }else{
        error.style.display= "block";
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




