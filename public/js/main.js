import { Button } from "./components/Button.js";
import { Input } from "./components/Input.js";
import { CheckBox } from "./components/checkbox.js";
import { Navbar } from "./components/Navbar.js";
import { Slider } from "./components/Slider.js";

// Setup lucide icons
lucide.createIcons(
    {

        attrs:{
            'stroke-width': 1.5,
            'width': '24px',
            'height': '24px',
        }
    }
);

// Define the custom elements
customElements.define("x-checkbox", CheckBox);
customElements.define("x-input", Input);
customElements.define("x-button", Button);
customElements.define("x-navbar", Navbar);
customElements.define("x-slider", Slider);
