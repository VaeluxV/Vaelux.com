document.addEventListener("DOMContentLoaded", () => {
    const mediaGrid = document.getElementById("media-grid");
    const typeFilters = document.querySelectorAll(".type-filter");
    const tagFilters = document.querySelectorAll(".tag-filter");
    const clearFiltersButton = document.querySelector(".clear-filters");
    const sidebar = document.getElementById("sidebar");
    const toggleSidebarButton = document.getElementById("toggle-sidebar");
    const searchInput = document.getElementById("search-input");
    const searchButton = document.getElementById("search-button");
    const selectedTagsCount = document.getElementById("selected-tags-count");
    const searchSuggestions = document.getElementById("search-suggestions");
    const mobileCloseSidebar = document.getElementById("mobile-close-sidebar");
    const vehicleTypesToggle = document.getElementById("vehicle-types-toggle");
    const vehicleTypesContent = document.getElementById("vehicle-types-content");
    const locationsToggle = document.getElementById("locations-toggle");
    const locationsContent = document.getElementById("locations-content");
    const mediaTypesToggle = document.getElementById("media-types-toggle");
    const mediaTypesContent = document.getElementById("media-types-content");
    const contentCategoriesToggle = document.getElementById("content-categories-toggle");
    const contentCategoriesContent = document.getElementById("content-categories-content");
    const trainTypesToggle = document.getElementById("train-types-toggle");
    const trainTypesContent = document.getElementById("train-types-content");
    const nmbsSncbToggle = document.getElementById("nmbs-sncb-toggle");
    const nmbsSncbContent = document.getElementById("nmbs-sncb-content");
    const nsToggle = document.getElementById("ns-toggle");
    const nsContent = document.getElementById("ns-content");
    const eurostarToggle = document.getElementById("eurostar-toggle");
    const eurostarContent = document.getElementById("eurostar-content");
    const sncfToggle = document.getElementById("sncf-toggle");
    const sncfContent = document.getElementById("sncf-content");
    const dbToggle = document.getElementById("db-toggle");
    const dbContent = document.getElementById("db-content");
    const otherTrainsToggle = document.getElementById("other-trains-toggle");
    const otherTrainsContent = document.getElementById("other-trains-content");
    const main = document.querySelector('main');
    
    // Tag matching mode elements
    const anyTagsModeButton = document.getElementById("any-tags-mode");
    const allTagsModeButton = document.getElementById("all-tags-mode");

    // Sidebar toggle behavior
    toggleSidebarButton.addEventListener("click", () => {
        if (window.innerWidth > 580 && !window.matchMedia("(orientation: portrait)").matches) {
            // Desktop: animate fade out/in
            if (sidebar.classList.contains("closed")) {
                sidebar.classList.remove("closed");
                sidebar.classList.add("sidebar-animating");
                sidebar.style.opacity = "0";
                sidebar.style.visibility = "hidden";
                setTimeout(() => {
                    sidebar.style.opacity = "1";
                    sidebar.style.visibility = "visible";
                }, 10);
                setTimeout(() => {
                    sidebar.classList.remove("sidebar-animating");
                    sidebar.style.opacity = "";
                    sidebar.style.visibility = "";
                }, 310);
                main.classList.remove('sidebar-closed');
            } else {
                sidebar.classList.add("sidebar-animating");
                sidebar.style.opacity = "0";
                sidebar.style.visibility = "hidden";
                setTimeout(() => {
                    sidebar.classList.add("closed");
                    sidebar.classList.remove("sidebar-animating");
                    sidebar.style.opacity = "";
                    sidebar.style.visibility = "";
                    main.classList.add('sidebar-closed');
                }, 300);
            }
        } else {
            // Mobile: instant open/close
            sidebar.classList.toggle("closed");
        }
        resetSidebarScroll();
    });

    // Mobile close button behavior
    mobileCloseSidebar.addEventListener("click", () => {
        sidebar.classList.add("closed");
        if (window.innerWidth > 580 && !window.matchMedia("(orientation: portrait)").matches) {
            main.classList.add('sidebar-closed');
        }
    });

    // Auto-close sidebar on mobile/portrait
    if (window.innerWidth <= 580 || window.matchMedia("(orientation: portrait)").matches) {
        sidebar.classList.add("closed");
        main.classList.remove('sidebar-closed');
    } else if (sidebar.classList.contains('closed')) {
        main.classList.add('sidebar-closed');
    } else {
        main.classList.remove('sidebar-closed');
    }

    // Handle orientation changes
    window.addEventListener("orientationchange", () => {
        setTimeout(() => {
            if (window.innerWidth <= 580 || window.matchMedia("(orientation: portrait)").matches) {
                sidebar.classList.add("closed");
                main.classList.remove('sidebar-closed');
            } else if (sidebar.classList.contains('closed')) {
                main.classList.add('sidebar-closed');
            } else {
                main.classList.remove('sidebar-closed');
            }
        }, 100);
    });

    // Reset sidebar scroll position on page load
    window.addEventListener("load", () => {
        sidebar.scrollTop = 0;
    });

    // Reset sidebar scroll position when it opens on mobile/portrait
    const resetSidebarScroll = () => {
        if (window.innerWidth <= 580 || window.matchMedia("(orientation: portrait)").matches) {
            sidebar.scrollTop = 0;
        }
    };

    // Set up all collapsible sections
    const setupCollapsible = (toggle, content, startCollapsed = false) => {
        toggle.addEventListener("click", () => {
            toggle.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        });
        
        if (startCollapsed) {
            toggle.classList.add("collapsed");
            content.classList.add("collapsed");
        }
    };

    // Initialize all collapsible sections
    setupCollapsible(mediaTypesToggle, mediaTypesContent, false); // Media Types expanded by default
    setupCollapsible(locationsToggle, locationsContent, true);
    setupCollapsible(vehicleTypesToggle, vehicleTypesContent, true);
    setupCollapsible(trainTypesToggle, trainTypesContent, true);
    setupCollapsible(contentCategoriesToggle, contentCategoriesContent, true);
    setupCollapsible(nmbsSncbToggle, nmbsSncbContent, true);
    setupCollapsible(nsToggle, nsContent, true);
    setupCollapsible(eurostarToggle, eurostarContent, true);
    setupCollapsible(sncfToggle, sncfContent, true);
    setupCollapsible(dbToggle, dbContent, true);
    setupCollapsible(otherTrainsToggle, otherTrainsContent, true);

    // Cache for search suggestions
    let allTags = new Set();
    let searchCache = new Map();

    // Load media data
    fetch("/media-library/data/media.json")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Validate data structure
            if (!Array.isArray(data)) {
                throw new Error("Invalid data format: expected array");
            }
            
            // Extract all unique tags for suggestions
            data.forEach(item => {
                if (item && item.tags && Array.isArray(item.tags)) {
                    item.tags.forEach(tag => {
                        if (typeof tag === 'string' && tag.trim()) {
                            allTags.add(tag.trim());
                        }
                    });
                }
            });
            
            renderMedia(data);
            setupFilters(data);
        })
        .catch(error => {
            console.error("Error loading media data:", error);
            // Show user-friendly error message
            mediaGrid.innerHTML = '<div class="media-card no-results"><h3>Error Loading Media</h3><p>Unable to load media data. Please try refreshing the page.</p></div>';
        });

    // For media cards: fade in/out
    function fadeOutCard(card) {
        card.classList.add("fade-out");
        setTimeout(() => {
            card.style.display = "none";
        }, 300);
    }
    function fadeInCard(card) {
        card.style.display = "";
        setTimeout(() => {
            card.classList.remove("fade-out");
        }, 10);
    }

    // Comprehensive input sanitization function
    function sanitizeHTML(str) {
        if (typeof str !== 'string') return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Validate and sanitize URLs
    function sanitizeURL(url) {
        if (typeof url !== 'string') return '';
        // Only allow relative paths and trusted domains
        const allowedDomains = ['youtube.com', 'www.youtube.com'];
        const urlObj = new URL(url, window.location.origin);
        
        // Allow relative paths
        if (url.startsWith('/')) return url;
        
        // Allow trusted domains
        if (allowedDomains.includes(urlObj.hostname)) return url;
        
        return '';
    }

    // Render media items
    function renderMedia(mediaItems) {
        // Fade out cards that will be removed
        const existingCards = Array.from(mediaGrid.children);
        existingCards.forEach(card => {
            if (!mediaItems.some(item => card.getAttribute("data-type") === item.type && card.getAttribute("data-tags") === (Array.isArray(item.tags) ? item.tags.join(" ") : ""))) {
                fadeOutCard(card);
            }
        });
        setTimeout(() => {
            mediaGrid.innerHTML = "";
            if (!Array.isArray(mediaItems) || mediaItems.length === 0) {
                const noResultsCard = document.createElement("div");
                noResultsCard.classList.add("media-card", "no-results");
                noResultsCard.innerHTML = `
                    <h3>No Results Found</h3>
                    <p>There are no media items matching the current filter(s). Try adjusting your filters.</p>
                `;
                mediaGrid.appendChild(noResultsCard);
                fadeInCard(noResultsCard);
                return;
            }
            mediaItems.forEach(item => {
                if (!item || typeof item !== 'object') return;
                const mediaCard = document.createElement("div");
                mediaCard.classList.add("media-card");
                
                // Sanitize all user data
                const type = item.type && typeof item.type === 'string' ? item.type : 'unknown';
                const tags = Array.isArray(item.tags) ? item.tags.join(" ") : "";
                const title = sanitizeHTML(item.title && typeof item.title === 'string' ? item.title : 'Untitled');
                const descriptionRaw = item.description && typeof item.description === 'string' ? item.description : '';
                const description = sanitizeHTML(descriptionRaw).replace(/\n/g, '<br>');
                const src = sanitizeURL(item.src && typeof item.src === 'string' ? item.src : '');
                
                mediaCard.setAttribute("data-type", type);
                mediaCard.setAttribute("data-tags", tags);
                
                if (type === "image") {
                    mediaCard.innerHTML = `
                        <img src="${src}" alt="${title}" loading="lazy">
                        <h3>${title}</h3>
                        <p>${description}</p>
                    `;
                } else if (type === "video") {
                    mediaCard.innerHTML = `
                        <iframe src="${src}" frameborder="0" allowfullscreen loading="lazy"></iframe>
                        <h3>${title}</h3>
                        <p>${description}</p>
                    `;
                } else {
                    mediaCard.innerHTML = `
                        <div class="media-placeholder">${sanitizeHTML(type.toUpperCase())}</div>
                        <h3>${title}</h3>
                        <p>${description}</p>
                    `;
                }
                mediaGrid.appendChild(mediaCard);
                fadeInCard(mediaCard);
            });
        }, 300);
    }

    // Set up filters
    function setupFilters(mediaItems) {
        let selectedTypes = new Set(["image", "video", "other"]); // Track selected media types (all selected by default)
        let selectedTags = new Set(); // Track multiple selected tags
        let searchQuery = ""; // Track search query
        let tagMatchingMode = "any"; // Track tag matching mode: "any" or "all"

        // Set up tag matching mode toggle
        anyTagsModeButton.addEventListener("click", () => {
            tagMatchingMode = "any";
            anyTagsModeButton.classList.add("active");
            allTagsModeButton.classList.remove("active");
            applyFilters();
        });

        allTagsModeButton.addEventListener("click", () => {
            tagMatchingMode = "all";
            allTagsModeButton.classList.add("active");
            anyTagsModeButton.classList.remove("active");
            applyFilters();
        });

        // Set up search functionality with suggestions
        searchInput.addEventListener("input", (e) => {
            searchQuery = e.target.value;
            showSearchSuggestions(e.target.value);
            applyFilters();
        });

        searchButton.addEventListener("click", () => {
            searchQuery = searchInput.value;
            hideSearchSuggestions();
            applyFilters();
        });

        // Allow Enter key to trigger search
        searchInput.addEventListener("keypress", (e) => {
            if (e.key === "Enter") {
                searchQuery = searchInput.value;
                hideSearchSuggestions();
                applyFilters();
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener("click", (e) => {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                hideSearchSuggestions();
            }
        });

        // Show search suggestions
        function showSearchSuggestions(query) {
            if (!query || typeof query !== 'string' || !query.trim()) {
                hideSearchSuggestions();
                return;
            }

            const suggestions = getSearchSuggestions(query.trim());
            if (suggestions.length === 0) {
                hideSearchSuggestions();
                return;
            }

            // Sanitize suggestions before rendering
            searchSuggestions.innerHTML = suggestions.map(tag => {
                const sanitizedTag = sanitizeHTML(tag);
                return `<div class="suggestion-item" data-tag="${sanitizedTag}">${sanitizedTag}</div>`;
            }).join("");

            // Add click handlers to suggestions
            searchSuggestions.querySelectorAll(".suggestion-item").forEach(item => {
                item.addEventListener("click", () => {
                    const tag = item.getAttribute("data-tag");
                    if (tag && !selectedTags.has(tag)) {
                        selectedTags.add(tag);
                        applyFilters();
                    }
                    searchInput.value = "";
                    hideSearchSuggestions();
                });
            });

            searchSuggestions.style.display = "block";
        }

        // Hide search suggestions
        function hideSearchSuggestions() {
            searchSuggestions.style.display = "none";
        }

        // Get search suggestions with caching
        function getSearchSuggestions(query) {
            if (!query || typeof query !== 'string') {
                return [];
            }
            
            const cacheKey = query.toLowerCase();
            
            if (searchCache.has(cacheKey)) {
                return searchCache.get(cacheKey);
            }

            const suggestions = Array.from(allTags)
                .filter(tag => typeof tag === 'string' && tag.toLowerCase().includes(cacheKey))
                .slice(0, 5); // Limit to 5 suggestions

            searchCache.set(cacheKey, suggestions);
            return suggestions;
        }

        // Apply the combined filters
        function applyFilters() {
            let filteredItems = mediaItems;

            // Filter by selected types
            filteredItems = filteredItems.filter(item => selectedTypes.has(item.type));

            // Filter by selected tags based on matching mode
            if (selectedTags.size > 0) {
                if (tagMatchingMode === "all") {
                    // ALL tags must be present (refinement logic)
                    filteredItems = filteredItems.filter(item => {
                        return Array.from(selectedTags).every(tag => item.tags.includes(tag));
                    });
                } else {
                    // ANY tag can be present (expansion logic)
                    filteredItems = filteredItems.filter(item => {
                        return Array.from(selectedTags).some(tag => item.tags.includes(tag));
                    });
                }
            }

            // Filter by search query
            if (searchQuery.trim() !== "") {
                const query = searchQuery.toLowerCase().trim();
                filteredItems = filteredItems.filter(item => {
                    return item.title.toLowerCase().includes(query) ||
                           item.description.toLowerCase().includes(query) ||
                           item.tags.some(tag => tag.toLowerCase().includes(query));
                });
            }

            renderMedia(filteredItems);
            updateActiveFilterDisplay();
        }

        // Update the display of active filters
        function updateActiveFilterDisplay() {
            // Update type filter buttons
            typeFilters.forEach(button => {
                const buttonType = button.getAttribute("data-type");
                if (selectedTypes.has(buttonType)) {
                    button.classList.add("active");
                } else {
                    button.classList.remove("active");
                }
            });

            // Update tag filter buttons
            tagFilters.forEach(button => {
                const buttonTag = button.getAttribute("data-tag");
                if (selectedTags.has(buttonTag)) {
                    button.classList.add("active");
                } else {
                    button.classList.remove("active");
                }
            });

            // Update selected tags counter with mode indicator
            const count = selectedTags.size;
            const modeText = tagMatchingMode === "all" ? "ALL" : "ANY";
            if (count === 0) {
                selectedTagsCount.textContent = "No tags selected";
            } else if (count === 1) {
                selectedTagsCount.textContent = `1 tag selected (${modeText})`;
            } else {
                selectedTagsCount.textContent = `${count} tags selected (${modeText})`;
            }
        }

        // Set up type filters (multi-select)
        typeFilters.forEach(button => {
            button.addEventListener("click", () => {
                const type = button.getAttribute("data-type");
                if (selectedTypes.has(type)) {
                    selectedTypes.delete(type);
                } else {
                    selectedTypes.add(type);
                }
                applyFilters();
            });
        });

        // Set up tag filters (multi-select)
        tagFilters.forEach(button => {
            button.addEventListener("click", () => {
                const tag = button.getAttribute("data-tag");
                if (selectedTags.has(tag)) {
                    selectedTags.delete(tag);
                } else {
                    selectedTags.add(tag);
                }
                applyFilters();
            });
        });

        // Set up clear filters button
        clearFiltersButton.addEventListener("click", () => {
            selectedTypes = new Set(["image", "video", "other"]); // Reset to all types
            selectedTags.clear();
            searchQuery = "";
            tagMatchingMode = "any"; // Reset to default mode
            anyTagsModeButton.classList.add("active");
            allTagsModeButton.classList.remove("active");
            applyFilters();
        });

        // Initialize filter display
        updateActiveFilterDisplay();
    }
});
