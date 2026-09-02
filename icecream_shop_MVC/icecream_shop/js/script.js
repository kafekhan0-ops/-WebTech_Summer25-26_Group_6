document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("menuToggle");
    const nav = document.getElementById("mainNav");

    if (toggle && nav) {
        toggle.addEventListener("click", function () {
            nav.classList.toggle("open");
        });
    }

    setTimeout(function () {
        document.querySelectorAll(".flash").forEach(function (el) {
            el.style.opacity = "0";
            el.style.transition = "opacity .5s";
            setTimeout(() => el.remove(), 500);
        });
    }, 3500);
});
