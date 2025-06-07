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
const currentState = "<?php echo isset($user['state']) ? $user['state'] : ''; ?>";
const currentCity = "<?php echo isset($user['city']) ? $user['city'] : ''; ?>";

for(let state in cityByState){
        let option = document.createElement("option");
        option.value = state;
        option.textContent = state;
        if(state === currentState) {
            option.selected = true;
        }
        selected_state.appendChild(option);
    }

    // If there's a current state, populate its cities
    if(currentState && cityByState[currentState]) {
        selected_city.innerHTML = '<option value="">Select City</option>';
        cityByState[currentState].forEach(city => {
            let option = document.createElement("option");
            option.value = city;
            option.textContent = city;
            if(city === currentCity) {
                option.selected = true;
            }
            selected_city.appendChild(option);
        });
    }

    selected_state.addEventListener("change", function(){
        const state = this.value;
        selected_city.innerHTML = '<option value="">Select City</option>';

        if(state in cityByState) {
            cityByState[state].forEach(city => {
                let option = document.createElement("option");
                option.value = city;
                option.textContent = city;
                selected_city.appendChild(option);
            });
        }
    });
});

