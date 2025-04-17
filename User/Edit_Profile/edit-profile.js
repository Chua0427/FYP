document.addEventListener('DOMContentLoaded', function() {
  
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