// Dashboard form handling with AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Display name form submission
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('display_name', this.querySelector('#display_name').value);
            formData.append('profile_picture', document.getElementById('profile_picture').value);
            
            fetch('/api/update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Display name updated successfully!', 'success');
                    // Update the welcome message
                    const welcomeH1 = document.querySelector('h1');
                    if (welcomeH1) {
                        welcomeH1.textContent = 'Welcome, ' + formData.get('display_name') + '!';
                    }
                } else {
                    showMessage(data.error || 'Failed to update display name', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while updating display name', 'error');
            });
        });
    }

    // Profile picture form submission
    const profilePictureForm = document.getElementById('profile-picture-form');
    if (profilePictureForm) {
        profilePictureForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('display_name', document.getElementById('display_name').value);
            formData.append('profile_picture', document.getElementById('profile_picture').value);
            
            fetch('/api/update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Profile picture updated successfully!', 'success');
                } else {
                    showMessage(data.error || 'Failed to update profile picture', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while updating profile picture', 'error');
            });
        });
    }

    // Email form submission
    const emailForm = document.getElementById('email-form');
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/api/update_email.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Email updated successfully!', 'success');
                    this.reset();
                } else {
                    showMessage(data.error || 'Failed to update email', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while updating email', 'error');
            });
        });
    }

    // Password form submission
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/api/update_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Password updated successfully!', 'success');
                    this.reset();
                } else {
                    showMessage(data.error || 'Failed to update password', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while updating password', 'error');
            });
        });
    }

    // Helper function to show messages
    function showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.dashboard-message');
        existingMessages.forEach(msg => msg.remove());

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `dashboard-message ${type}`;
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            ${type === 'success' ? 
                'background: #1a4d1a; color: #90ee90; border: 1px solid #4caf50;' : 
                'background: #4d1a1a; color: #ffb3b3; border: 1px solid #f44336;'
            }
        `;

        // Insert at the top of the dashboard container
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            dashboardContainer.insertBefore(messageDiv, dashboardContainer.firstChild);
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
}); 