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
    const strengthContainer = document.querySelector('.password-strength');
    const strengthText = document.getElementById('strengthText');
    const bars = document.querySelectorAll('.strength-bar');
    
    // Get requirement icons
    const lengthIcon = document.querySelector('#lengthReq i');
    const uppercaseIcon = document.querySelector('#uppercaseReq i'); 
    const numberIcon = document.querySelector('#numberReq i');
    const specialIcon = document.querySelector('#specialReq i');
    
    // Reset all states
    [lengthIcon, uppercaseIcon, numberIcon, specialIcon].forEach(icon => {
        icon.className = 'fas fa-circle';
    });
    bars.forEach(bar => {
        bar.style.width = '0%';
        bar.className = 'strength-bar';
    });

    if (password.length === 0) {
        strengthContainer.style.display = 'none';
        return;
    } else {
        strengthContainer.style.display = 'block';
    }

    // Update requirement icons
    // Length requirement
    if (password.length >= 8) {
        lengthIcon.className = 'fas fa-check';
    }
    
    // Uppercase requirement
    if (/[A-Z]/.test(password)) {
    console.log('检测到大写字母'); // 调试用
    uppercaseIcon.className = 'fas fa-check';
}
    
    // Number requirement
    if (/\d/.test(password)) {
        numberIcon.className = 'fas fa-check';
    }
    
    // Special character requirement
    if (/[^A-Za-z0-9]/.test(password)) {
        specialIcon.className = 'fas fa-check';
    }

    // Calculate strength (0-4)
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    // Update strength meter
    let strengthLabel = '';
    let strengthClass = '';
    
    if (strength <= 1) {
        strengthLabel = 'Weak';
        strengthClass = 'weak';
        bars[0].style.width = '25%';
    } else if (strength <= 2) {
        strengthLabel = 'Medium';
        strengthClass = 'medium';
        bars[0].style.width = '25%';
        bars[1].style.width = '25%';
    } else if (strength <= 3) {
        strengthLabel = 'Strong';
        strengthClass = 'strong';
        bars[0].style.width = '25%';
        bars[1].style.width = '25%';
        bars[2].style.width = '25%';
    } else {
        strengthLabel = 'Very Strong';
        strengthClass = 'very-strong';
        bars.forEach(bar => bar.style.width = '25%');
    }

    strengthText.textContent = `Password Strength: ${strengthLabel}`;
    bars.forEach((bar, index) => {
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

// Enhanced form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorMessages = [];
    
    // Validation checks
    if (newPassword.length < 8) {
        errorMessages.push('Password must be at least 8 characters');
    }
    if (!/[A-Z]/.test(newPassword)) {
        errorMessages.push('Must contain at least one uppercase letter');
    }
    if (!/\d/.test(newPassword)) {
        errorMessages.push('Must contain at least one number');
    }
    if (!/[^A-Za-z0-9]/.test(newPassword)) {
        errorMessages.push('Must contain at least one special character');
    }
    if (newPassword !== confirmPassword) {
        errorMessages.push('Passwords do not match');
    }

    // Prevent submission if errors
    if (errorMessages.length > 0) {
        e.preventDefault();
        alert('Please fix the following issues:\n\n- ' + errorMessages.join('\n- '));
    }
});