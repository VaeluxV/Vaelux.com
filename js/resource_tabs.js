document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".resource-tab");
    const tabContents = document.querySelectorAll(".resource-tab-content");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");

            tabContents.forEach(content => {
                content.classList.remove("active");
                if (content.id === tab.dataset.tab) {
                    content.classList.add("active");
                }
            });
        });
    });
});
