console.log("🔥 products.js loaded");

document.addEventListener("DOMContentLoaded", () => {

    // CREATE
    const form = document.querySelector("form[data-action='create']");
    if (form) {
        form.addEventListener("submit", () => {
            console.log("🟢 Product Created");
        });
    }

    // EDIT
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", () => {
            console.log("🔵 Edit clicked | Product ID:", btn.dataset.id);
        });
    });

    // DELETE
    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", () => {
            console.log("🔴 Delete clicked | Product ID:", btn.dataset.id);
        });
    });

    // DOWNLOAD
    document.querySelectorAll(".btn-download").forEach(btn => {
        btn.addEventListener("click", () => {
            console.log("🟢 Download clicked | Product ID:", btn.dataset.id);
        });
    });

});