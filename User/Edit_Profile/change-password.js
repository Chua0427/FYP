function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const strengthMeter = document.getElementById('strengthMeter');
    const requirements = {
        length: document.getElementById('lengthReq'),
        number: document.getElementById('numberReq'),
        special: document.getElementById('specialReq')
    };
    
    newPassword.addEventListener('input', function() {
        const password = newPassword.value;
        let strength = 0;
        
        // Check length
        if (password.length >= 8) {
            requirements.length.querySelector('i').className = 'fas fa-check valid';
            strength += 33;
        } else {
            requirements.length.querySelector('i').className = 'fas fa-circle invalid';
        }
        
        // Check for numbers
        if (/\d/.test(password)) {
            requirements.number.querySelector('i').className = 'fas fa-check valid';
            strength += 33;
        } else {
            requirements.number.querySelector('i').className = 'fas fa-circle invalid';
        }
        
        // Check for special characters
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            requirements.special.querySelector('i').className = 'fas fa-check valid';
            strength += 34;
        } else {
            requirements.special.querySelector('i').className = 'fas fa-circle invalid';
        }
        
        // Update strength meter
        strengthMeter.style.width = strength + '%';
        if (strength < 33) {
            strengthMeter.style.backgroundColor = '#f44336';
        } else if (strength < 66) {
            strengthMeter.style.backgroundColor = '#ff9800';
        } else {
            strengthMeter.style.backgroundColor = '#4CAF50';
        }
        
        // Check password match
        checkPasswordMatch();
    });
    
    confirmPassword.addEventListener('input', checkPasswordMatch);
    
    function checkPasswordMatch() {
        const matchElement = document.getElementById('passwordMatch');
        if (newPassword.value && confirmPassword.value) {
            if (newPassword.value === confirmPassword.value) {
                matchElement.textContent = 'Passwords match';
                matchElement.style.color = '#4CAF50';
            } else {
                matchElement.textContent = 'Passwords do not match';
                matchElement.style.color = '#f44336';
            }
        } else {
            matchElement.textContent = '';
        }
    }
    
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (newPassword.value !== confirmPassword.value) {
            alert('Passwords do not match!');
            return;
        }
        
        // Here you would send the data to your backend
        // const formData = new FormData(this);
        // fetch('/change-password', { method: 'POST', body: formData })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         alert('Password changed successfully!');
        //         window.location.href = 'profile.html';
        //     } else {
        //         alert('Error: ' + data.message);
        //     }
        // });
        
        alert('Password changed successfully! (This is a demo)');
        window.location.href = 'profile.html';
    });
});
