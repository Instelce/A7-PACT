<<<<<<< HEAD
import {Button} from "./components/Button.js";
import {Input} from "./components/Input.js";
=======
<<<<<<< HEAD
import {Button} from "./components/Button.js";
import {Input} from "./components/Input.js";
import {CheckBox} from "./components/checkbox.js";
import {Navbar} from "./components/Navbar.js";
import {Footer} from "./components/Footer.js";
=======
import { Button } from "./components/Button.js";
import { Input } from "./components/form/Input.js";
import { Checkbox } from "./components/form/Checkbox.js";
import { Navbar } from "./components/Navbar.js";
import { Slider } from "./components/form/Slider.js";
import {Select} from "./components/form/Select.js";
>>>>>>> 3175943714acfda805b1415c163381eb7a40e285
>>>>>>> 43a5bcc01982b08daf7056081b6b96b9de666b2e

lucide.createIcons(
    {

        attrs:{
            'stroke-width': 1,
            'width': '18px',
            'height': '18px',
        }
    }
); 


// Define the custom elements
<<<<<<< HEAD
customElements.define('x-input', Input);
customElements.define('x-button', Button);
=======
<<<<<<< HEAD
customElements.define('x-checkbox', CheckBox);
customElements.define('x-input', Input);
customElements.define('x-button', Button);
customElements.define('x-navbar', Navbar);
customElements.define('x-footer', Footer);



=======
customElements.define("x-checkbox", Checkbox);
customElements.define("x-input", Input);
customElements.define("x-button", Button);
customElements.define("x-navbar", Navbar);
customElements.define("x-slider", Slider);
customElements.define("x-select", Select);
>>>>>>> 3175943714acfda805b1415c163381eb7a40e285
>>>>>>> 43a5bcc01982b08daf7056081b6b96b9de666b2e
