document.addEventListener("DOMContentLoaded", () => {
    const mediaGrid = document.getElementById("media-grid");
    const typeFilters = document.querySelectorAll(".type-filter");
    const tagFilters = document.querySelectorAll(".tag-filter");
    const clearFiltersButton = document.querySelector(".clear-filters");
    const sidebar = document.getElementById("sidebar");
    const toggleSidebarButton = document.getElementById("toggle-sidebar");

    // Sidebar toggle behavior
    toggleSidebarButton.addEventListener("click", () => {
        sidebar.classList.toggle("closed");
        toggleSidebarButton.innerHTML = sidebar.classList.contains("closed") ? "&#9664;" : "&#9654;";
    });

    // Auto-close sidebar on mobile
    if (window.innerWidth <= 768) {
        sidebar.classList.add("closed");
    }

    // Load media data
    fetch("/media-library/data/media.json")
        .then(response => response.json())
        .then(data => {
            renderMedia(data);
            setupFilters(data);
        })
        .catch(error => console.error("Error loading media data:", error));

    // Render media items
    function renderMedia(mediaItems) {
        mediaGrid.innerHTML = ""; // Clear existing content

        if (mediaItems.length === 0) {
            // Display a "no results" message
            const noResultsCard = document.createElement("div");
            noResultsCard.classList.add("media-card", "no-results");
            noResultsCard.innerHTML = `
                <h3>No Results Found</h3>
                <p>There are no media items matching the current filter(s). Try adjusting your filters.</p>
            `;
            mediaGrid.appendChild(noResultsCard);
        } else {
            // Render media items
            mediaItems.forEach(item => {
                const mediaCard = document.createElement("div");
                mediaCard.classList.add("media-card");
                mediaCard.setAttribute("data-type", item.type); // Set data attribute for type-based filtering
                mediaCard.setAttribute("data-tags", item.tags.join(" ")); // Set data attribute for tag-based filtering

                if (item.type === "image") {
                    mediaCard.innerHTML = `
                        <img src="${item.src}" alt="${item.title}">
                        <h3>${item.title}</h3>
                        <p>${item.description}</p>
                    `;
                } else if (item.type === "video") {
                    mediaCard.innerHTML = `
                        <iframe src="${item.src}" frameborder="0" allowfullscreen></iframe>
                        <h3>${item.title}</h3>
                        <p>${item.description}</p>
                    `;
                }

                mediaGrid.appendChild(mediaCard);
            });
        }
    }

    // Set up filters
    function setupFilters(mediaItems) {
        let currentType = "all"; // Track the current type filter
        let currentTag = "all";  // Track the current tag filter

        // Apply the combined filters
        function applyFilters() {
            let filteredItems = mediaItems;

            // Filter by type
            if (currentType !== "all") {
                filteredItems = filteredItems.filter(item => item.type === currentType);
            }

            // Filter by tag
            if (currentTag !== "all") {
                filteredItems = filteredItems.filter(item => item.tags.includes(currentTag));
            }

            renderMedia(filteredItems);
        }

        // Set up type filters
        typeFilters.forEach(button => {
            button.addEventListener("click", () => {
                currentType = button.getAttribute("data-type");
                applyFilters();
            });
        });

        // Set up tag filters
        tagFilters.forEach(button => {
            button.addEventListener("click", () => {
                currentTag = button.getAttribute("data-tag");
                applyFilters();
            });
        });

        // Set up clear filters button
        clearFiltersButton.addEventListener("click", () => {
            currentType = "all";
            currentTag = "all";
            renderMedia(mediaItems);
        });
    }
});
