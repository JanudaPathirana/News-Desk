// NSBM NewsDesk Client Scripts (v2.0)

document.addEventListener('DOMContentLoaded', function () {
    // 1. Auto-dismiss alerts after 5 seconds  
    setTimeout(function () {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function (alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // 2. Navbar shadow when page is scrolled  
    var nav = document.querySelector('.navbar-green');
    window.addEventListener('scroll', function () {
        if (nav) nav.classList.toggle('scrolled', window.scrollY > 10);
    });

    // 3. Scroll animation - fade in items when they become visible  
    var items = document.querySelectorAll(
        '.card, .box, .section-title, .table-responsive, .btn-outline-dark'
    );

    var canObserve = 'IntersectionObserver' in window;

    if (canObserve) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        items.forEach(function (el, i) {
            if (el.getBoundingClientRect().top < window.innerHeight) return;

            el.classList.add('reveal');

            // Small delay so cards in a row appear one by one  
            el.style.transitionDelay = (i % 3) * 0.1 + 's';

            observer.observe(el);
        });
    }

    // 4. Count up animation for admin dashboard numbers  
    document.querySelectorAll('.big-number').forEach(function (el) {
        var end = parseInt(el.textContent.replace(/,/g, ''), 10);

        if (isNaN(end) || end === 0) return;

        var now = 0;
        var step = Math.max(1, Math.ceil(end / 40));

        var timer = setInterval(function () {
            now += step;

            if (now >= end) {
                now = end;
                clearInterval(timer);
            }

            el.textContent = now;
        }, 25);
    });
});

// 5. Image preview helper (Global function outside DOMContentLoaded so it can be called from HTML `onchange`)
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            document.getElementById(previewId).src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}