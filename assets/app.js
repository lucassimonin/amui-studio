/* =========================================================
   Front amuï — icônes, année courante, ancres
   ========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide?.createIcons) window.lucide.createIcons();

    const year = document.getElementById('year');
    if (year) year.textContent = String(new Date().getFullYear());
});
