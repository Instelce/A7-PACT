import {Button} from "./components/Button.js";
import {Input} from "./components/Input.js";

lucide.createIcons(); 


// Define the custom elements
customElements.define('x-input', Input);
customElements.define('x-button', Button);