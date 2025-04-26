document.addEventListener('DOMContentLoaded', function() {   
    const userData = {
        first_name: 'Sodddh',
        last_name: 'Xi Jie',
        email: 'john.doe@example.com',
        mobile_number: '1234567890',
        address: '123 Main St, Apt 4B',
        postcode: '12345',
        state: 'California',
        city: 'Los Angeles',
        birthday_date: '1990-01-01',
        gender: 'Male',
        profile_image: 'default-profile.jpg',
        create_at: '2023-01-01'
    };
document.getElementById('firstName').value = userData.first_name;
document.getElementById('lastName').value = userData.last_name;
document.getElementById('email').value = userData.email;
document.getElementById('mobileNumber').value = userData.mobile_number;
document.getElementById('address').value = userData.address;
document.getElementById('postcode').value = userData.postcode;
document.getElementById('state').value = userData.state;
document.getElementById('city').value = userData.city;
document.getElementById('birthday').value = userData.birthday_date;
document.getElementById('gender').value = userData.gender;
document.getElementById('profileImage').src = userData.profile_image;
document.getElementById('displayName').textContent = `${userData.first_name} ${userData.last_name}`;
document.getElementById('memberSince').textContent = new Date(userData.create_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long' });

document.getElementById('profileImageInput').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('profileImage').src = event.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});

document.getElementById('editProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Profile updated successfully!');
});
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

