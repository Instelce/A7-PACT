import { WebComponent } from './WebComponent.js';

/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * @arg {boolean} hasbutton - Determines if the input field has an associated button.
 * @arg {boolean} rounded - Determines if the input field has rounded corners.
 * @arg {string} txtButton - Text to display on the button if `hasbutton` is true.
 * 
 * @extends {WebComponent}
 */
export class Input extends WebComponent {
    static get observedAttributes() {
        return ['placeholder', 'hasbutton', 'rounded','txtButton'];
    }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.updateRender();
    }

    connectedCallback() {
        super.connectedCallback();
    }

    disconnectedCallback() {
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this.updateRender();
        }
    }

    styles() {
        const rounded = this.getAttribute('rounded') === 'true' ? '200px' : '5px';
        return `
            <style>
                div {
                    display: flex;
                    align-items: center;
                    border: 1px solid #ccc;
                    border-radius: ${rounded};
                    padding: 10px 20px;
                    width: 160px;
                    height: 20px;
                    margin: 5px;
                    color: #838383;
                }
                input {
                    border: none;
                    outline: none;
                    flex: 1;
                    padding: 5px;
                    width: 100%;
                }
                x-button {
                    width: 50px;
                    height: 29px;
                }
            </style>
        `;
    }

    render() {
        const placeholder = this.getAttribute('placeholder');
        const hasButton = this.getAttribute('hasbutton') === 'true';
        const txtButton = this.getAttribute('txtButton');

        return `
            <div>
                <slot name="icon-left"></slot>
                <input type="text" placeholder="${placeholder}" />
                <slot name="icon-right"></slot>
                ${hasButton ? `<x-button>${txtButton}</x-button>` : ''}
            </div>
        `;
    }
    
    updateRender() {
        this.shadowRoot.innerHTML = this.styles() + this.render();
    }
}