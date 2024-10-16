import {WebComponent} from "./WebComponent.js";

export class CheckBox extends WebComponent {

    static get observedAttributes() { return [] }

    constructor() {
        super();

        this.colorPro();
    }

    connectedCallback() {
        super.connectedCallback();
    }

    disconnectedCallback() {
        super.connectedCallback();
    }

    attributeChangedCallback (name, oldValue, newValue) {}

    styles() {
        return `
        <style>
            div
            {
                display: flex;
                align-items: space-between;
            }

            input 
            {
                -webkit-appearance: none;
                appearance: none;
                margin: 0;

                display: grid;
                place-content: center;
                grid-template colomn

                font: inherit;
                color: rgb(var(--color-gray-2));
                width: 2em;
                height: 2em;
                border: 1px solid rgb(var(--color-gray-2));
                border-radius: 0.15em;
            }
              
            input::before 
            {
                content: "";
                width: 2em;
                height: 2em;
                transform: scale(0);
                transition: 120ms transform ease-in-out;
                box-shadow: inset 1em 1em var(--form-control-color);
            }

            input:checked::before
            {
                transform: scale(0.7);
                background-color: rgb(var(--color-purple-primary));
                border-radius: 0.15em;
            }
            label
            {
                margin-left: 10px
            }
        </style>
        `;
    }

    render() {
        const labelTexte = this.getAttribute('labelTexte');
        return `
            <div>
                <input type="checkbox" id="checkBox" name="labels" />
                <label for="checkBox">${labelTexte}</label>
                
            </div>
        `;
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //

    colorPro()
    {
        if (this.isPro == true)
        {
            this.addStyleVariable("background", "rgb(var(--color-purple-primary))");
            this.addStyleVariable("color", "rgb(var(--color-white))");
        }
        else
        {
            this.addStyleVariable("background", "rgb(var(--color-blue-primary))");
            this.addStyleVariable("color", "rgb(var(--color-white))");
        
        }
    }

    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

    get isPro() {
        return this.hasAttribute('pro');
    }

}