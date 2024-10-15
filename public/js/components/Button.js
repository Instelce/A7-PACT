import {WebComponent} from "./WebComponent.js";


/**
 * Button component
 *
 * @arg {string} color - Color of the button. Default is blue
 */
export class Button extends WebComponent {

    static get observedAttributes() {
        return []
    }

    constructor() {
        super();

        this.resolveColor();
    }

    connectedCallback() {
        super.connectedCallback();
    }

    disconnectedCallback() {
    }

    attributeChangedCallback(name, oldValue, newValue) {
    }

    styles() {
        return `
            <style>              
               button {
                padding: .8rem 2rem;
                
                background: var(--background);
                border: 1px solid var(--border);
                border-radius: var(--radius-rounded);
                
                font-size: inherit;
                color: var(--color);
                font-weight: 500;
                
                display:flex;
                gap: 1rem;
                
                cursor: pointer;
                transition: color .2s, background .2s, border .2s;
               }
               
               button:hover {
                background: var(--background-hover);
                border: 1px solid var(--border-hover);
                color: var(--color-hover);
               }
            </style>
        `;
    }

    render() {
        return `
            <button>
              <slot name="icon-left"></slot>
              <slot></slot>
              <slot name="icon-right"></slot>
            </button>
        `;
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //

    resolveColor() {
        switch (this.color) {
            case "gray":
                this.addStyleVariable("background", "rgb(var(--color-white))");
                this.addStyleVariable("border", "rgb(var(--color-gray-2))");
                this.addStyleVariable("color", "rgb(var(--color-black))");

                this.addStyleVariable("background-hover", "rgb(var(--color-gray-2))");
                this.addStyleVariable("border-hover", "rgb(var(--color-gray-2))");
                this.addStyleVariable("color-hover", "rgb(var(--color-black))");
                break;
            case "danger":
                this.addStyleVariable("background", "rgb(var(--color-danger))");
                this.addStyleVariable("border", "rgb(var(--color-danger))");
                this.addStyleVariable("color", "rgb(var(--color-white))");

                this.addStyleVariable("background-hover", "rgb(var(--color-white))");
                this.addStyleVariable("border-hover", "rgb(var(--color-danger))");
                this.addStyleVariable("color-hover", "rgb(var(--color-danger))");
                break;
            case "purple":
                this.addStyleVariable("background", "rgb(var(--color-purple-primary))");
                this.addStyleVariable("border", "rgb(var(--color-purple-primary))");
                this.addStyleVariable("color", "rgb(var(--color-white))");

                this.addStyleVariable("background-hover", "rgb(var(--color-white))");
                this.addStyleVariable("border-hover", "rgb(var(--color-purple-primary))");
                this.addStyleVariable("color-hover", "rgb(var(--color-purple-primary))");
                break;
            default:
                this.addStyleVariable("background", "rgb(var(--color-blue-primary))");
                this.addStyleVariable("border", "rgb(var(--color-blue-primary))");
                this.addStyleVariable("color", "rgb(var(--color-white))");

                this.addStyleVariable("background-hover", "rgb(var(--color-white))");
                this.addStyleVariable("border-hover", "rgb(var(--color-blue-primary))");
                this.addStyleVariable("color-hover", "rgb(var(--color-blue-primary))");
        }
    }

    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

    /**
     * @returns {"gray"|"blue"|"purple"|"danger"}
     */
    get color() {
        return this.getAttribute('color') ?? "blue";
    }

    /**
     * @returns {boolean}
     */
    get filled() {
        return this.hasAttribute('fill');
    }
}