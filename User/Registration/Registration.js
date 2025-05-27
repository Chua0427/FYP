document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded, running Registration.js");

    // 密码可见性切换功能
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

    // 新增密码有效性状态
    let isPasswordValid = false;

    function checkPasswordStrength(password) {
        const requirementsMet = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // 更新图标状态
        requirements.length.querySelector('i').className = 
            requirementsMet.length ? 'fas fa-check' : 'fas fa-circle';
        requirements.upper.querySelector('i').className = 
            requirementsMet.upper ? 'fas fa-check' : 'fas fa-circle';
        requirements.number.querySelector('i').className = 
            requirementsMet.number ? 'fas fa-check' : 'fas fa-circle';
        requirements.special.querySelector('i').className = 
            requirementsMet.special ? 'fas fa-check' : 'fas fa-circle';

        // 检查是否满足所有条件
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

        // 密码有效性反馈
        if (passwordValue) {
            if (!isPasswordValid) {
                passwordFeedback.textContent = 'Please fulfill all password requirements';
                passwordFeedback.className = 'password-feedback invalid';
            } else {
                passwordFeedback.textContent = 'All requirements met!';
                passwordFeedback.className = 'password-feedback valid';
            }
        }

        // 确认密码验证
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

    // 表单提交验证
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        let errorMessages = [];

        // 密码有效性检查
        if (!isPasswordValid) {
            errorMessages.push('Password must meet all requirements');
            passwordFeedback.classList.add('invalid');
        }

        // 密码匹配检查
        if (password.value !== confirmPassword.value) {
            errorMessages.push('Passwords do not match');
            confirmFeedback.classList.add('invalid');
        }

        if (errorMessages.length > 0) {
            e.preventDefault();
            alert('Registration Error:\n' + errorMessages.join('\n'));
        }
    });

    // 邮箱实时验证（未修改，但独立运行）
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value;
        const emailError = document.getElementById('emailError');
        
        if (!email) {
            emailError.style.display = 'none';
            return;
        }
        
        // 基础邮箱格式验证
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailError.style.display = 'block';
            emailError.textContent = 'Please enter a valid email address';
            return;
        }
        
        // 异步检查邮箱是否存在
        fetch('check_email.php?email=' + encodeURIComponent(email))
            .then(response => response.json())
            .then(data => {
                emailError.style.display = data.exists ? 'block' : 'none';
                emailError.textContent = data.exists ? 'This email is already registered' : '';
            });
    });

    // 文件上传验证（未修改）
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.match('image.*')) {
                alert('Please select an image file');
                this.value = '';
            } else if (file.size > 2000000) {
                alert('Image size should be less than 2MB');
                this.value = '';
            }
        }
    });
});