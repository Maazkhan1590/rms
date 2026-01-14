// Authentication functionality

document.addEventListener('DOMContentLoaded', () => {
    // Login form handling
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = this.parentElement.querySelector('input');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });

    // Login form submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const rememberMe = document.getElementById('remember-me').checked;

            // Clear previous errors
            clearErrors();

            // Simple validation
            let isValid = true;

            if (!validateEmail(email)) {
                showError('email-error', 'Please enter a valid email address');
                isValid = false;
            }

            if (password.length < 6) {
                showError('password-error', 'Password must be at least 6 characters');
                isValid = false;
            }

            if (isValid) {
                // Simulate login process
                showLoading(loginForm);

                // In a real application, this would be an API call
                setTimeout(() => {
                    hideLoading(loginForm);

                    // For demo purposes, simulate successful login
                    // Store user info in localStorage (in a real app, use more secure methods)
                    localStorage.setItem('isLoggedIn', 'true');
                    localStorage.setItem('userEmail', email);

                    // Redirect to home page
                    window.location.href = 'index.html';
                }, 1500);
            }
        });
    }

    // Register form submission
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form values
            const firstName = document.getElementById('first-name').value;
            const lastName = document.getElementById('last-name').value;
            const email = document.getElementById('register-email').value;
            const affiliation = document.getElementById('affiliation').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const researchField = document.getElementById('research-field').value;
            const termsAccepted = document.getElementById('terms').checked;

            // Clear previous errors
            clearErrors();

            // Validation
            let isValid = true;

            if (firstName.trim().length < 2) {
                showError('first-name-error', 'First name must be at least 2 characters');
                isValid = false;
            }

            if (lastName.trim().length < 2) {
                showError('last-name-error', 'Last name must be at least 2 characters');
                isValid = false;
            }

            if (!validateEmail(email)) {
                showError('register-email-error', 'Please enter a valid email address');
                isValid = false;
            }

            if (affiliation.trim().length < 3) {
                showError('affiliation-error', 'Please enter a valid affiliation');
                isValid = false;
            }

            if (password.length < 8) {
                showError('register-password-error', 'Password must be at least 8 characters');
                isValid = false;
            }

            if (password !== confirmPassword) {
                showError('confirm-password-error', 'Passwords do not match');
                isValid = false;
            }

            if (!termsAccepted) {
                alert('You must agree to the Terms of Service and Privacy Policy');
                isValid = false;
            }

            if (isValid) {
                // Simulate registration process
                showLoading(registerForm);

                // In a real application, this would be an API call
                setTimeout(() => {
                    hideLoading(registerForm);

                    // Store user data in localStorage (for demo purposes only)
                    const userData = {
                        firstName,
                        lastName,
                        email,
                        affiliation,
                        researchField,
                        registeredAt: new Date().toISOString()
                    };

                    localStorage.setItem('userData', JSON.stringify(userData));
                    localStorage.setItem('isLoggedIn', 'true');

                    // Show success message
                    alert('Registration successful! Welcome to the Research Portal.');

                    // Redirect to home page
                    window.location.href = 'index.html';
                }, 2000);
            }
        });
    }

    // Social auth buttons
    const socialAuthButtons = document.querySelectorAll('.btn-social');
    socialAuthButtons.forEach(button => {
        button.addEventListener('click', function() {
            const provider = this.classList.contains('google') ? 'Google' : 'ORCID';
            alert(`In a real application, this would redirect to ${provider} authentication.`);
        });
    });

    // Helper functions
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function clearErrors() {
        const errorElements = document.querySelectorAll('.form-error');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }

    function showLoading(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitButton.disabled = true;

        // Store original content for later restoration
        submitButton.setAttribute('data-original', originalText);
    }

    function hideLoading(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.getAttribute('data-original');

        if (originalText) {
            submitButton.innerHTML = originalText;
        }
        submitButton.disabled = false;
    }

    // Check if user is logged in (for future enhancements)
    function checkAuthStatus() {
        const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';

        // Update UI based on auth status
        if (isLoggedIn) {
            const userEmail = localStorage.getItem('userEmail');
            const navMenu = document.querySelector('.nav-menu');

            if (navMenu) {
                // Update login link to profile/dashboard
                const loginLink = navMenu.querySelector('a[href="login.html"]');
                if (loginLink) {
                    loginLink.textContent = 'Dashboard';
                    loginLink.href = '#';
                }
            }
        }
    }

    // Check auth status on page load
    checkAuthStatus();
});