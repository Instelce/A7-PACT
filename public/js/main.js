import { Input } from "./components/form/Input.js";
import { Slider } from "./components/form/Slider.js";
import { SearchPageCard } from "./components/SearchPageCard.js";
import {Select} from "./components/form/Select.js";
import {Carousel} from "./components/Carousel.js";
import { Acordeon } from "./components/Acordeon.js";
import {Tabs} from "./components/tabs/Tabs.js";
import {Tab} from "./components/tabs/Tab.js";
import {Panel} from "./components/tabs/Panel.js";

// Do not change
lucide.createIcons({
    attrs: {
        'stroke-width': 1.5,
        'width': '24px',
        'height': '24px'
    }
});


customElements.define("x-input", Input);
customElements.define("x-slider", Slider);
customElements.define("x-select", Select);
customElements.define("x-search-page-card", SearchPageCard);
customElements.define("x-acordeon", Acordeon);
customElements.define("x-carousel", Carousel);


customElements.define('x-tabs', Tabs);
customElements.define('x-tab', Tab);
customElements.define('x-tab-panel', Panel)


// Loader
const loader = document.querySelector(".loader");
if (loader) {
    document.addEventListener("DOMContentLoaded", () => {
        loader.classList.add("hidden");
    });
}


// Navbar
let navbar = document.querySelector('.navbar');
let heightTop = document.querySelector('.height-top');
let navIcon = document.getElementById('nav-burger');
let menu = document.getElementById('menu');

if (navIcon) {
    navIcon.addEventListener('click', function () {
        navIcon.classList.toggle('open');
        menu.classList.toggle('menu-hidden');
        menu.classList.toggle('menu-visible');
    });
}

if (navbar && heightTop) {
    heightTop.style.height = navbar.offsetHeight + 'px';
}