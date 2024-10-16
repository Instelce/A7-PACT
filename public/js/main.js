import {Button} from "./components/Button.js";
import {Input} from "./components/Input.js";
import {Switch} from "./components/Switch.js";

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
customElements.define('x-input', Input);
customElements.define('x-button', Button);
customElements.define('x-toggle', Switch);