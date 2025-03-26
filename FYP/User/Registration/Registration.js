// Password visibility toggler
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

// Setup for both password fields
setupPasswordToggle('password', 'togglePassword');
setupPasswordToggle('confirmPassword', 'toggleConfirmPassword');

// Real-time password validation
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const feedbackElements = document.querySelectorAll('.password-feedback');

function validatePasswords() {
    const passwordValue = password.value;
    const confirmValue = confirmPassword.value;
    
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

password.addEventListener('input', validatePasswords);
confirmPassword.addEventListener('input', validatePasswords);

// Update original submit validation
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    if (password.value !== confirmPassword.value) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});