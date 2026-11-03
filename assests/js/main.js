// small js for animations (no library)
document.addEventListener('DOMContentLoaded', function () {

  // navbar shadow when page is scrolled
  var nav = document.querySelector('.navbar-green');
  window.addEventListener('scroll', function () {
    if (nav) nav.classList.toggle('scrolled', window.scrollY > 10);
  });

  // scroll animation - items that are below the screen fade in when we scroll to them
  var items = document.querySelectorAll('.card, .box, .section-title, .table-responsive, .btn-outline-dark');
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
      if (el.getBoundingClientRect().top < window.innerHeight) return; // already visible
      el.classList.add('reveal');
      // small delay so cards in a row come one by one
      el.style.transitionDelay = (i % 3) * 0.1 + 's';
      observer.observe(el);
    });
  }

  // count up for the numbers in the admin dashboard
  document.querySelectorAll('.big-number').forEach(function (el) {
    var end = parseInt(el.textContent.replace(/,/g, ''), 10);
    if (isNaN(end) || end === 0) return;
    var now = 0;
    var step = Math.max(1, Math.ceil(end / 40));
    var timer = setInterval(function () {
      now += step;
      if (now >= end) { now = end; clearInterval(timer); }
      el.textContent = now;
    }, 25);
  });
});
