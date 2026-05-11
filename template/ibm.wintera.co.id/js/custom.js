document.addEventListener('DOMContentLoaded', function() {
    
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    }, { passive: true });

    const $carousel = $('#testimonialCarousel');
    if ($carousel.length > 0) {
        $carousel.carousel({
            interval: 5000,
            pause: "hover"
        });
    }

    const calendarEl = document.querySelector("#calendar-inline");
    if (calendarEl) {
        flatpickr(calendarEl, {
            inline: true,
            monthSelectorType: "static",
            static: true
        });
    }
});