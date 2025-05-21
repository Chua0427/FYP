<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | VeroSports</title>
    <link rel="stylesheet" href="registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <main class="container">
        <h1>Create Your Account</h1>
        <form action="Registration.php" method="POST" enctype="multipart/form-data" id="registrationForm" class="registration-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Enter your first name">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                    <span id="emailError" class="password-feedback invalid">Please enter a valid email address</span>
                </div>
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="tel" id="mobile_number" name="mobile_number" pattern="[0-9]{10,11}" required placeholder="e.g. 0123456789">
                </div>

                <div class="form-group">
                    <label for="password">Create Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" minlength="8" required placeholder="Create a strong password">
                        <i class="fa-solid fa-eye" id="togglePassword"></i>
                    </div>
                    <div class="password-strength">
                        <span class="strength-text">Password Strength:</span>
                        <div class="strength-bars">
                            <span class="strength-bar"></span>
                            <span class="strength-bar"></span>
                            <span class="strength-bar"></span>
                        </div>
                    </div>
                    <div class="requirements">
                        <div class="requirement" id="lengthReq">
                            <i class="fas fa-circle"></i>
                            <span>At least 8 characters</span>
                        </div>
                        <div class="requirement" id="upperReq">
                            <i class="fas fa-circle"></i>
                            <span>At least 1 uppercase letter</span>
                        </div>
                        <div class="requirement" id="numberReq">
                            <i class="fas fa-circle"></i>
                            <span>At least 1 number</span>
                        </div>
                        <div class="requirement" id="specialReq">
                            <i class="fas fa-circle"></i>
                            <span>At least 1 special character</span>
                        </div>
                    </div>
                    <span class="password-feedback"></span>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Re-enter your password">
                        <i class="fa-solid fa-eye" id="toggleConfirmPassword"></i>
                    </div>
                    <span class="password-feedback" id="confirmFeedback"></span>
                </div>

                <div class="form-group full-width">
                    <label for="address">Full Address</label>
                    <textarea id="address" name="address" rows="3" required placeholder="Enter your complete address"></textarea>
                </div>
                <div class="form-group">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" pattern="[0-9]{5}" required placeholder="e.g. 50000">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required placeholder="Enter your city">
                </div>
                <div class="form-group">
                    <label for="state">State</label>
                    <select id="state" name="state" required>
                        <option value="" disabled selected>Select your state</option>
                        <option value="Johor">Johor</option>
                        <option value="Kedah">Kedah</option>
                        <option value="Kelantan">Kelantan</option>
                        <option value="Kuala Lumpur">Kuala Lumpur</option>
                        <option value="Melaka">Melaka</option>
                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                        <option value="Pahang">Pahang</option>
                        <option value="Penang">Penang</option>
                        <option value="Perak">Perak</option>
                        <option value="Perlis">Perlis</option>
                        <option value="Sabah">Sabah</option>
                        <option value="Sarawak">Sarawak</option>
                        <option value="Selangor">Selangor</option>
                        <option value="Terengganu">Terengganu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="birthday_date">Date of Birth</label>
                    <input type="date" id="birthday_date" name="birthday_date" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <div class="gender-options">
                        <label>
                            <input type="radio" name="gender" value="male" required> Male
                        </label>
                        <label>
                            <input type="radio" name="gender" value="female"> Female
                        </label>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="profile_image">Profile Picture (Optional)</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
            </div>
            <div class="form-actions">
                <a href="../login/login.php" class="return-btn"><i class="fas fa-arrow-left"></i> Back to Login</a>
                <button type="submit" class="submit-btn">Register Now <i class="fas fa-arrow-right"></i></button>
            </div>
        </form>
    </main>
    <script src="Registration.js"></script>

    <script>
        // Password visibility toggle
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const password = document.querySelector('#password');
        const confirmPassword = document.querySelector('#confirmPassword');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        // Password strength checker
        password.addEventListener('input', function() {
            const value = this.value;
            const strengthBars = document.querySelectorAll('.strength-bar');
            const requirements = {
                length: value.length >= 8,
                upper: /[A-Z]/.test(value),
                number: /\d/.test(value),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(value)
            };

            // Update requirement icons
            document.getElementById('lengthReq').querySelector('i').className = 
                requirements.length ? 'fas fa-check' : 'fas fa-circle';
            document.getElementById('upperReq').querySelector('i').className = 
                requirements.upper ? 'fas fa-check' : 'fas fa-circle';
            document.getElementById('numberReq').querySelector('i').className = 
                requirements.number ? 'fas fa-check' : 'fas fa-circle';
            document.getElementById('specialReq').querySelector('i').className = 
                requirements.special ? 'fas fa-check' : 'fas fa-circle';

            // Calculate strength
            let strength = 0;
            if (requirements.length) strength++;
            if (requirements.upper) strength++;
            if (requirements.number) strength++;
            if (requirements.special) strength++;

            // Update strength bars
            strengthBars.forEach((bar, index) => {
                bar.className = 'strength-bar';
                if (strength > index) {
                    if (strength === 1) bar.classList.add('weak');
                    else if (strength === 2 || strength === 3) bar.classList.add('medium');
                    else if (strength === 4) bar.classList.add('strong');
                }
            });

            // Update feedback text
            const feedback = document.querySelector('.password-feedback');
            if (value.length === 0) {
                feedback.textContent = '';
                feedback.className = 'password-feedback';
            } else if (value.length < 8) {
                feedback.textContent = 'Password too short';
                feedback.className = 'password-feedback invalid';
            } else if (strength < 2) {
                feedback.textContent = 'Weak password';
                feedback.className = 'password-feedback invalid';
            } else if (strength < 4) {
                feedback.textContent = 'Medium strength';
                feedback.className = 'password-feedback valid';
            } else {
                feedback.textContent = 'Strong password!';
                feedback.className = 'password-feedback valid';
            }
        });

        // Confirm password validation
        confirmPassword.addEventListener('input', function() {
            const feedback = document.getElementById('confirmFeedback');
            if (this.value !== password.value) {
                feedback.textContent = 'Passwords do not match';
                feedback.className = 'password-feedback invalid';
            } else {
                feedback.textContent = 'Passwords match!';
                feedback.className = 'password-feedback valid';
            }
        });

        // Email validation
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        
        emailInput.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.value)) {
                emailError.style.display = 'block';
            } else {
                emailError.style.display = 'none';
            }
        });

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                document.getElementById('confirmFeedback').textContent = 'Passwords must match!';
                document.getElementById('confirmFeedback').className = 'password-feedback invalid';
                confirmPassword.focus();
            }
        });

        // Profile picture preview (optional enhancement)
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    this.value = '';
                } else if (file.size > 2000000) { // 2MB limit
                    alert('Image size should be less than 2MB');
                    this.value = '';
                }
            }
        });
    </script>
</body>
</html>