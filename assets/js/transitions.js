document.addEventListener("DOMContentLoaded", function () {
    function loadPage(url, addToHistory = true) {
        const content = document.getElementById("content");

        // Apply fade-out first
        content.classList.add("fade-out");

        setTimeout(() => {
            fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
                .then(response => response.text())
                .then(data => {
                    content.innerHTML = data;

                    // Remove fade-out and apply fade-in
                    content.classList.remove("fade-out");
                    content.classList.add("fade-in");

                    if (addToHistory) {
                        history.pushState({ page: url }, "", url);
                    }

                    attachEventListeners();
                })
                .catch(error => console.error("Error loading page:", error));
        }, 300); // Delay matches CSS animation duration
    }

    function attachEventListeners() {
        document.querySelectorAll(".nav-link").forEach(link => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                const url = this.getAttribute("href");
                loadPage(url);
            });
        });
    }

    window.onpopstate = function (event) {
        if (event.state) {
            loadPage(event.state.page, false);
        }
    };

    attachEventListeners();
});
