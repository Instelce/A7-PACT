import { Input } from "./components/form/Input.js";
import { Slider } from "./components/form/Slider.js";
import {SearchPageCard} from "./components/SearchPageCard.js";
import { Select } from "./components/form/Select.js";

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

