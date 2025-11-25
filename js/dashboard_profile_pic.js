// Profile picture selection logic for dashboard
document.addEventListener('DOMContentLoaded', function () {
    const currentPic = document.getElementById('current-profile-pic');
    const modal = document.getElementById('profile-pic-modal');
    const closeModal = document.getElementById('close-profile-pic-modal');
    const confirmModal = document.getElementById('confirm-profile-pic-modal');
    const picInput = document.getElementById('profile_picture');
    const grid = document.getElementById('profile-pic-grid');
    let selectedPic = null;

    function getTitle(path) {
        const name = path.split('/').pop().replace(/\.jpg$/i, '');
        return name;
    }

    function updateSelection() {
        // Remove selection from all options
        document.querySelectorAll('.profile-pic-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Add selection to current option
        if (selectedPic) {
            const selectedOption = document.querySelector(`[data-pic="${selectedPic}"]`);
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
        }
    }

    // Open modal
    currentPic.addEventListener('click', function () {
        selectedPic = picInput.value || window.currentProfilePic;
        updateSelection();
        modal.style.display = 'flex';
    });

    // Close modal without saving
    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
        selectedPic = null;
    });

    // Confirm selection
    confirmModal.addEventListener('click', function () {
        if (selectedPic) {
            currentPic.src = selectedPic;
            picInput.value = selectedPic;
        }
        modal.style.display = 'none';
        selectedPic = null;
    });

    // Handle option clicks
    document.querySelectorAll('.profile-pic-option').forEach(option => {
        option.addEventListener('click', function () {
            selectedPic = this.getAttribute('data-pic');
            updateSelection();
        });
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            selectedPic = null;
        }
    });

    // Initialize
    currentPic.src = window.currentProfilePic;
    picInput.value = window.currentProfilePic;
}); 