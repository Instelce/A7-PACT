import { WebComponent } from './WebComponent.js';
import { Button } from './Button.js';

/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * 
 */
export class Input extends WebComponent {
    static get observedAttributes() {
        return ['placeholder', 'hasbutton', 'rounded'];
    }

    constructor() {
        super();
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
                    align-items: space-between;
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

        return `
            <div>
                <slot name="icon-left">
                <slot>
                </slot>
                <input type="text" placeholder="${placeholder}" />
                ${hasButton ? '<x-button></x-button>' : ''}
                <slot name="icon-right">
                <slot>
                </slot>
            </div>
        `;
    }
    
    updateRender() {
        this.shadow.innerHTML = this.styles() + this.render();
    }
}
