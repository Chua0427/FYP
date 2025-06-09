document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded, running Registration.js");

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

    const selected_state = document.getElementById("state");
    const selected_city = document.getElementById("city");

    // Populate states dropdown
    for(let state in cityByState) {
        let option = document.createElement("option");
        option.value = state;
        option.textContent = state;
        selected_state.appendChild(option);
    }

    // Update cities when state changes
    selected_state.addEventListener("change", function() {
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

    // Password visibility toggle function
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

    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('confirmPassword', 'toggleConfirmPassword');

    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordFeedback = document.getElementById('passwordFeedback');
    const confirmFeedback = document.getElementById('confirmFeedback');

    const strengthBars = document.querySelectorAll('.strength-bar');
    const requirements = {
        length: document.getElementById('lengthReq'),
        upper: document.getElementById('upperReq'),
        number: document.getElementById('numberReq'),
        special: document.getElementById('specialReq')
    };

    // Track password validity state
    let isPasswordValid = false;

    function checkPasswordStrength(password) {
        const requirementsMet = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Update requirement icons
        requirements.length.querySelector('i').className = 
            requirementsMet.length ? 'fas fa-check' : 'fas fa-circle';
        requirements.upper.querySelector('i').className = 
            requirementsMet.upper ? 'fas fa-check' : 'fas fa-circle';
        requirements.number.querySelector('i').className = 
            requirementsMet.number ? 'fas fa-check' : 'fas fa-circle';
        requirements.special.querySelector('i').className = 
            requirementsMet.special ? 'fas fa-check' : 'fas fa-circle';

        // Check if all requirements are met
        isPasswordValid = Object.values(requirementsMet).every(v => v);
        return requirementsMet;
    }

    function updateStrengthMeter(requirementsMet) {
        strengthBars.forEach(bar => bar.className = 'strength-bar');
        const strength = Object.values(requirementsMet).filter(v => v).length;

        if (strength <= 1) {
            strengthBars[0].classList.add('weak');
        } else if (strength <= 3) {
            strengthBars.slice(0,2).forEach(b => b.classList.add('medium'));
        } else {
            strengthBars.forEach(b => b.classList.add('strong'));
        }
    }

    function validatePasswords() {
        const passwordValue = password.value;
        const requirementsMet = checkPasswordStrength(passwordValue);
        updateStrengthMeter(requirementsMet);

        // Password validity feedback
        if (passwordValue) {
            if (!isPasswordValid) {
                passwordFeedback.textContent = 'Please fulfill all password requirements';
                passwordFeedback.className = 'password-feedback invalid';
            } else {
                passwordFeedback.textContent = 'All requirements met!';
                passwordFeedback.className = 'password-feedback valid';
            }
        }

        // Password confirmation validation
        if (confirmPassword.value && passwordValue !== confirmPassword.value) {
            confirmFeedback.textContent = '✗ Passwords do not match';
            confirmFeedback.className = 'password-feedback invalid';
        } else if (confirmPassword.value) {
            confirmFeedback.textContent = '✓ Passwords match';
            confirmFeedback.className = 'password-feedback valid';
        }
    }

    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    function validateAge() {
        const birthdayInput = document.getElementById('birthday_date');
        const birthdayDate = new Date(birthdayInput.value);
        const today = new Date();
        const minAgeDate = new Date(
            today.getFullYear() - 18,
            today.getMonth(),
            today.getDate()
        );

        if (birthdayDate > minAgeDate) {
            alert('You must be at least 18 years old to register.');
            return false;
        }
        return true;
    }

    // Form submission validation
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        let errorMessages = [];

        // Password validity check
        if (!isPasswordValid) {
            errorMessages.push('Password must meet all requirements');
            passwordFeedback.classList.add('invalid');
        }

        // Password match check
        if (password.value !== confirmPassword.value) {
            errorMessages.push('Passwords do not match');
            confirmFeedback.classList.add('invalid');
        }
        
        // Age validation
        if (!validateAge()) {
            e.preventDefault();
            return;
        }

        if (errorMessages.length > 0) {
            e.preventDefault();
            alert('Registration Error:\n' + errorMessages.join('\n'));
        }
    });

    // Email validation
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value;
        const emailError = document.getElementById('emailError');
        
        if (!email) {
            emailError.style.display = 'none';
            return;
        }
        
        // Basic email format validation
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailError.style.display = 'block';
            emailError.textContent = 'Please enter a valid email address';
            return;
        }
        
        // Async email existence check
        fetch('check_email.php?email=' + encodeURIComponent(email))
            .then(response => response.json())
            .then(data => {
                emailError.style.display = data.exists ? 'block' : 'none';
                emailError.textContent = data.exists ? 'This email is already registered' : '';
            });
    });
});