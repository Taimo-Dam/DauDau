document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const passwordToggles = document.querySelectorAll('.toggle-password');
    
    // Password visibility toggle
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let errors = [];
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (password !== confirmPassword) {
            errors.push('Passwords do not match');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-error';
            errorDiv.textContent = errors.join(', ');
            form.insertBefore(errorDiv, form.firstChild);
        }
    });
});