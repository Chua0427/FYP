/**
 * Authentication Notification Utility
 * Shows popup notifications for login requirements
 */

document.addEventListener('DOMContentLoaded', function() {
    // Create notification container if it doesn't exist
    let notificationContainer = document.querySelector('.auth-notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.className = 'auth-notification-container';
        document.body.appendChild(notificationContainer);
    }

    // Remove any existing login modal to prevent duplicates
    const existingModal = document.querySelector('.login-modal');
    if (existingModal) {
        existingModal.remove();
    }

    // Create login notification modal
    const loginModal = document.createElement('div');
    loginModal.className = 'login-modal';
    loginModal.innerHTML = `
        <div class="login-modal-content">
            <span class="login-modal-close">&times;</span>
            <div class="login-modal-header">
                <h3>Login Required</h3>
            </div>
            <div class="login-modal-body">
                <p>Please login to continue with this action.</p>
                <div class="login-modal-buttons">
                    <a href="/FYP/FYP/User/login/login.php?redirect=${encodeURIComponent(window.location.pathname + window.location.search)}" class="login-button">Login</a>
                    <button class="cancel-button">Cancel</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loginModal);

    // Function to close modal
    function closeModal() {
        loginModal.style.display = 'none';
    }

    // Add event listeners to auth-required elements
    const authElements = document.querySelectorAll('[data-requires-auth="true"]');
    authElements.forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            loginModal.style.display = 'block';
        });
    });

    // Close modal when clicking close button (attach after modal is in DOM)
    const closeButton = loginModal.querySelector('.login-modal-close');
    if (closeButton) {
        closeButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
        });
    }

    // Close modal when clicking cancel button (attach after modal is in DOM)
    const cancelButton = loginModal.querySelector('.cancel-button');
    if (cancelButton) {
        cancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
        });
    }

    // Close modal when clicking outside the modal content
    loginModal.addEventListener('click', function(event) {
        // Check if clicked element is the modal background (not its children)
        if (event.target === loginModal) {
            closeModal();
        }
    });

    // Prevent clicks within the modal content from bubbling up to the modal
    const modalContent = loginModal.querySelector('.login-modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}); 