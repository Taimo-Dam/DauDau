document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Password visibility toggle
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = passwordInput.value.trim();

            if (!email || !password) {
                e.preventDefault();
                showError('Please fill in all fields');
            }
        });
    }

    // Error message helper
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-error';
        errorDiv.textContent = message;
        
        const existingError = document.querySelector('.alert-error');
        if (existingError) {
            existingError.remove();
        }
        
        form.insertBefore(errorDiv, form.firstChild);
    }
});