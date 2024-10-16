import {WebComponent} from "./WebComponent.js";


/**
 * footer component
 *
 * @arg {string} user - User connected to the website. Default is visitor
 */
export class Footer extends WebComponent {

    static get observedAttributes() {
        return []
    }

    constructor() {
        super();

        this.resolveUser();
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
               
            </style>
        `;
    }

    render() {
        return `
            
        `;
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //

    resolveUser() {
        switch (this.user) {
            case "pro":
                this.addStyleVariable("logo", "");
                this.addStyleVariable("color", "rgb(var(--color-purple-primary))");
                this.addStyleVariable("border", "none");

                this.addStyleVariable("color", "rgb(var(--color-purple-primary))");
                this.addStyleVariable("border", "1px var(--color-purple-primary)) solid inside bottom");
                break;
            default:
                this.addStyleVariable("logo", "");
                this.addStyleVariable("color", "rgb(var(--color-blue-primary))");
                this.addStyleVariable("border", "none");

                this.addStyleVariable("color", "rgb(var(--color-blue-primary))");
                this.addStyleVariable("border", "1px var(--color-blue-primary)) solid inside bottom");
        }
    }

    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

    /**
     * @returns {"pro"|"visitor"}
     */
    get user() {

    }

    /**
     * @returns {boolean}
     */
    get connected() {

    }
}