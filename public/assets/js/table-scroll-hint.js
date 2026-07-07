/* ============================================================
   Table Scroll Hint
   Toggles ".scrolled-end" on each .table-responsive wrapper so
   the CSS fade (Section 6b in mobile-responsive.css) disappears
   once the user has scrolled all the way to the right.
   Include this once, globally, e.g. at the bottom of your main
   layout file right before </body>.
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.table-responsive').forEach(function (el) {
        function checkScroll() {
            var atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 2;
            el.classList.toggle('scrolled-end', atEnd);
        }
        checkScroll();
        el.addEventListener('scroll', checkScroll);
        window.addEventListener('resize', checkScroll);
    });
});