import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

document.querySelector("#hamburger").addEventListener("click", function () {
    const menu = document.querySelector("#mobile-menu");
    menu.classList.toggle("hidden");
});
