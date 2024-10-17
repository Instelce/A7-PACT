import { Input } from "./components/form/Input.js";
import { Checkbox } from "./components/form/Checkbox.js";
import { Navbar } from "./components/Navbar.js";
import { Slider } from "./components/form/Slider.js";
import {Select} from "./components/form/Select.js";

lucide.createIcons({
    attrs:{
        'stroke-width': 1.5,
        'width': '24px',
        'height': '24px',
    }
});

// Define the custom elements
customElements.define("x-checkbox", Checkbox);
customElements.define("x-input", Input);
customElements.define("x-navbar", Navbar);
customElements.define("x-slider", Slider);
customElements.define("x-select", Select);
