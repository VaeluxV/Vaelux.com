document.addEventListener("DOMContentLoaded", () => {
    const screenshots = document.querySelectorAll(".screenshot");
    const modal = document.getElementById("fullscreen-modal");
    const modalImage = document.getElementById("fullscreen-image");
    const modalTitle = document.getElementById("modal-title");
    const modalInfo = document.getElementById("modal-info");
    const prevButton = document.getElementById("prev-image");
    const nextButton = document.getElementById("next-image");
    let currentImageIndex = 0;

    const openModal = (index) => {
        currentImageIndex = index;
        const screenshot = screenshots[index];
        modalImage.src = screenshot.src;
        modalTitle.textContent = screenshot.dataset.title;
        modalInfo.textContent = screenshot.dataset.info;
        modal.style.display = "flex";
    };

    const closeModal = () => {
        modal.style.display = "none";
    };

    const showPrevImage = () => {
        if (currentImageIndex > 0) {
            openModal(currentImageIndex - 1);
        }
    };

    const showNextImage = () => {
        if (currentImageIndex < screenshots.length - 1) {
            openModal(currentImageIndex + 1);
        }
    };

    screenshots.forEach((screenshot, index) => {
        screenshot.addEventListener("click", () => openModal(index));
    });

    prevButton.addEventListener("click", showPrevImage);
    nextButton.addEventListener("click", showNextImage);
    modal.addEventListener("click", closeModal);

    // Prevent closing modal when clicking on navigation buttons
    prevButton.addEventListener("click", (event) => event.stopPropagation());
    nextButton.addEventListener("click", (event) => event.stopPropagation());
});
