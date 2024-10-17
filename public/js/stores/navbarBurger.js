let navIcon = document.getElementById('nav-icon3');
let menu = document.getElementById('menu');
let closeMenu = document.getElementById('close-menu');

navIcon.addEventListener('click', function () {
    navIcon.classList.toggle('open');
    menu.classList.toggle('menu-hidden');
    menu.classList.toggle('menu-visible');
});

closeMenu.addEventListener('click', function () {
    menu.classList.remove('menu-visible');
    menu.classList.add('menu-hidden');
    navIcon.classList.remove('open');
});