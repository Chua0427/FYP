document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded, running Registration.js");

    // Password visibility toggler
    function setupPasswordToggle(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);
        
        if (!passwordInput || !eyeIcon) {
            console.error(`Could not find elements for password toggle: ${inputId}, ${iconId}`);
            return;
        }
        
        eyeIcon.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Setup for both password fields
    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('confirmPassword', 'toggleConfirmPassword');

    // Real-time password validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const feedbackElements = document.querySelectorAll('.password-feedback');

    // Password strength indicators
    const strengthBars = document.querySelectorAll('.strength-bar');
    const strengthText = document.querySelector('.strength-text');
    const requirements = {
        length: document.getElementById('lengthReq'),
        upper: document.getElementById('upperReq'),
        number: document.getElementById('numberReq'),
        special: document.getElementById('specialReq')
    };

    console.log("Password strength elements:", {
        strengthBars: strengthBars,
        strengthText: strengthText,
        requirements: requirements
    });

    function checkPasswordStrength(password) {
        console.log("Checking password strength for:", password);
        
        // Reset all requirements to circles first
        Object.values(requirements).forEach(req => {
            if (req && req.querySelector('i')) {
                req.querySelector('i').className = 'fas fa-circle';
            }
        });

        if (!password) return;

        // Check length
        if (password.length >= 8 && requirements.length && requirements.length.querySelector('i')) {
            requirements.length.querySelector('i').className = 'fas fa-check';
        }
        
        // Check uppercase
        if (/[A-Z]/.test(password) && requirements.upper && requirements.upper.querySelector('i')) {
            requirements.upper.querySelector('i').className = 'fas fa-check';
        }
        
        // Check numbers
        if (/\d/.test(password) && requirements.number && requirements.number.querySelector('i')) {
            requirements.number.querySelector('i').className = 'fas fa-check';
        }
        
        // Check special characters
        if (/[^A-Za-z0-9]/.test(password) && requirements.special && requirements.special.querySelector('i')) {
            requirements.special.querySelector('i').className = 'fas fa-check';
        }
        
        // Update strength meter
        updateStrengthMeter(password);
    }

    function updateStrengthMeter(password) {
        if (!strengthBars || !strengthText) return;

        // Reset all bars
        strengthBars.forEach(bar => {
            bar.className = 'strength-bar';
        });

        if (!password || password.length === 0) {
            strengthText.textContent = 'Strength:';
            return;
        }

        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        if (strength <= 1) {
            strengthText.textContent = 'Strength: Weak';
            strengthBars[0].classList.add('weak');
        } else if (strength <= 2) {
            strengthText.textContent = 'Strength: Medium';
            strengthBars[0].classList.add('medium');
            strengthBars[1].classList.add('medium');
        } else {
            strengthText.textContent = 'Strength: Strong';
            strengthBars[0].classList.add('strong');
            strengthBars[1].classList.add('strong');
            strengthBars[2].classList.add('strong');
        }
    }

    function validatePasswords() {
        const passwordValue = password?.value;
        const confirmValue = confirmPassword?.value;
        
        // Check password strength
        checkPasswordStrength(passwordValue);
        
        feedbackElements.forEach(element => {
            if (passwordValue && confirmValue) {
                if (passwordValue === confirmValue) {
                    element.classList.add('valid');
                    element.classList.remove('invalid');
                    element.textContent = '✓ Passwords match';
                } else {
                    element.classList.add('invalid');
                    element.classList.remove('valid');
                    element.textContent = '✗ Passwords do not match';
                }
            } else {
                element.textContent = '';
            }
        });
    }

    if (password && confirmPassword) {
        password.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    } else {
        console.error("Could not find password or confirmPassword elements");
    }

    const form = document.getElementById('registrationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (password?.value !== confirmPassword?.value) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    } else {
        console.error("Could not find registrationForm");
    }
});
// Add this to your Registration.js file
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value;
    const emailError = document.getElementById('emailError');
    
    if (!email) {
        emailError.style.display = 'none';
        return;
    }
    
    // Simple email validation
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailError.style.display = 'block';
        emailError.textContent = 'Please enter a valid email address';
        return;
    }
    
    // Check if email exists via AJAX
    fetch('check_email.php?email=' + encodeURIComponent(email))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                emailError.style.display = 'block';
                emailError.textContent = 'This email is already registered';
            } else {
                emailError.style.display = 'none';
            }
        });
});