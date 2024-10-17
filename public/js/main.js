import { Input } from "./components/form/Input.js";
import { Slider } from "./components/form/Slider.js";
import {SearchPageCard} from "./components/SearchPageCard.js";
import {Select} from "./components/form/Select.js";
// import {Tabs} from "./components/tabs/Tabs";
// import {Tab} from "./components/tabs/Tab";

// Do not change
lucide.createIcons({
    attrs:{
        'stroke-width': 1.5,
        'width': '24px',
        'height': '24px'
    }
});


customElements.define("x-input", Input);
customElements.define("x-navbar", Navbar);
customElements.define("x-slider", Slider);
customElements.define("x-select", Select);
customElements.define("x-search-page-card", SearchPageCard);


// customElements.define('x-tabs', Tabs);
// customElements.define('x-tab', Tab);


// Loader
document.addEventListener("DOMContentLoaded", () => {
    const loader = document.querySelector(".loader");
    loader.classList.add("hidden");
});


// Navbar
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