import { Button } from "./components/Button.js";
import { Input } from "./components/form/Input.js";
import { Slider } from "./components/form/Slider.js";
import {SearchPageCard} from "./components/SearchPageCard.js";
import { Select } from "./components/form/Select.js";


lucide.createIcons(
    {

        attrs: {
            'stroke-width': 1,
            'width': '18px',
            'height': '18px',
        }
    }
);



customElements.define("x-input", Input);
customElements.define("x-button", Button);
customElements.define("x-slider", Slider);
customElements.define("x-select", Select);
customElements.define("x-search-page-card", SearchPageCard);

