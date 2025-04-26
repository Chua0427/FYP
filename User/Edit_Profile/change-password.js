function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
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

// Password strength checker with requirement icons
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.getElementById('strengthText');
    const bars = document.querySelectorAll('.strength-bar');
    
    // Reset all bars
    bars.forEach(bar => {
        bar.style.width = '0%';
        bar.className = 'strength-bar';
    });
    
    // Get requirement icons
    const lengthIcon = document.querySelector('#lengthReq i');
    const numberIcon = document.querySelector('#numberReq i');
    const specialIcon = document.querySelector('#specialReq i');
    
    if (password.length === 0) {
        strengthText.textContent = 'Password Strength: ';
        // Reset all icons to circle
        lengthIcon.className = 'fas fa-circle';
        numberIcon.className = 'fas fa-circle';
        specialIcon.className = 'fas fa-circle';
        return;
    }
    
    // Check requirements and update icons
    // Length requirement
    if (password.length >= 8) {
        lengthIcon.className = 'fas fa-check';
    } else {
        lengthIcon.className = 'fas fa-circle';
    }
    
    // Number requirement
    if (/\d/.test(password)) {
        numberIcon.className = 'fas fa-check';
    } else {
        numberIcon.className = 'fas fa-circle';
    }
    
    // Special character requirement
    if (/[^A-Za-z0-9]/.test(password)) {
        specialIcon.className = 'fas fa-check';
    } else {
        specialIcon.className = 'fas fa-circle';
    }
    
    // Calculate strength
    let strength = 0;
    if (password.length >= 8) strength += 1;
    if (/[A-Z]/.test(password)) strength += 1;
    if (/[0-9]/.test(password)) strength += 1;
    if (/[^A-Za-z0-9]/.test(password)) strength += 1;
    
    // Update UI
    let strengthLabel = '';
    let strengthClass = '';
    
    if (strength <= 1) {
        strengthLabel = 'Weak';
        strengthClass = 'weak';
        bars[0].style.width = '33%';
    } else if (strength <= 2) {
        strengthLabel = 'Medium';
        strengthClass = 'medium';
        bars[0].style.width = '33%';
        bars[1].style.width = '33%';
    } else {
        strengthLabel = 'Strong';
        strengthClass = 'strong';
        bars[0].style.width = '33%';
        bars[1].style.width = '33%';
        bars[2].style.width = '33%';
    }
    
    strengthText.textContent = `Password Strength: ${strengthLabel}`;
    bars.forEach(bar => {
        if (bar.style.width !== '0%') {
            bar.classList.add(strengthClass);
        }
    });
});

// Confirm password match
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    const matchText = document.getElementById('passwordMatch');
    
    if (confirmPassword.length === 0) {
        matchText.textContent = '';
        matchText.className = 'validation-message';
    } else if (newPassword === confirmPassword) {
        matchText.textContent = 'Passwords match';
        matchText.className = 'validation-message valid';
    } else {
        matchText.textContent = 'Passwords do not match';
        matchText.className = 'validation-message invalid';
    }
});

// Form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match');
        e.preventDefault();
        return;
    }
    
    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters long');
        e.preventDefault();
        return;
    }
});

