document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".tab");
    const tabContents = document.querySelectorAll(".tab-content");

    const subTabs = document.querySelectorAll(".sub-tab");
    const subTabContents = document.querySelectorAll(".sub-tab-content");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            const target = document.querySelector(`#${tab.getAttribute("data-tab")}`);
            
            tabContents.forEach(tc => tc.classList.remove("active"));
            tabs.forEach(t => t.classList.remove("active"));

            tab.classList.add("active");
            target.classList.add("active");
        });
    });

    subTabs.forEach(subTab => {
        subTab.addEventListener("click", () => {
            const target = document.querySelector(`#${subTab.getAttribute("data-sub-tab")}`);
            
            subTabContents.forEach(stc => stc.classList.remove("active"));
            subTabs.forEach(st => st.classList.remove("active"));

            subTab.classList.add("active");
            target.classList.add("active");
        });
    });
});
