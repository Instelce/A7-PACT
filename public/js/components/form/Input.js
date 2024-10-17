import { WebComponent } from '../WebComponent.js';
import {useId} from "../../utils/id.js";

/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * @arg {string} name - Name of the input field.
 * @arg {boolean} rounded - ToggleSwitch rounded corners for the input field.
 * @arg {string} value - Value of the input
 *
 * @extends {WebComponent}
 */
export class Input extends WebComponent {

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();

        let input = this.querySelector('[slot="input"]');

        // Focus input when clicking on the field
        let field = this.shadow.querySelector('.input-field');
        field.addEventListener('click', () => {
            input.focus();
        });

        // Add focus class when input is focused
        input.addEventListener('focus', () => {
            field.classList.add('focused');
        });

        // Remove focus class when input is blurred
        input.addEventListener('blur', () => {
            field.classList.remove('focused');
        });
    }

    disconnectedCallback() {
    }

    styles() {
        const rounded = this.isRounded ? 'var(--radius-rounded)' : 'var(--radius-small)';
        return `
            <style>
                .input-field {
                    display: flex;
                    gap: .5rem;
                    align-items: center;
                    border: 1px solid #ccc;
                    border-radius: ${rounded};
                    padding: .5rem;
                    color: #838383;
                    
                    transition: box-shadow .2s;
                }
                
                .input-field.focused {
                    box-shadow: 0 0 0 1px rgb(var(--color-gray-2));
                }
                
                ::slotted([slot="input"]) {
                    height: 100%;
                    width: 100%;
                    border: none;
                    outline: none;
                    padding: .5rem;
                    
                    font-family: inherit;
                    font-size: inherit;
                }
                
                .icon-left {
                  padding-left: .5rem;
                }
                .icon-right {
                  padding-right: .5rem;
                }
                
                ::slotted([slot="helper"]) {
                  color: rgb(var(--color-gray-4));
                }
                
                ::slotted([slot="error"]) {
                    color: rgb(var(--color-danger));
                    font-weight: 500;
                }
            </style>
        `;
    }

    render() {
        return `
            <div class="input-field">
                ${this.hasIconLeft ? `<div class="icon-left">
                    <slot name="icon-left"></slot>   
                </div>` : ''}
                
                <slot name="input"></slot>
                <slot name="button"></slot>
                
                ${this.hasIconRight ? `<div class="icon-right">
                    <slot name="icon-right"></slot>   
                </div>` : ''}
            </div>
            
            <slot name="helper"></slot>
            <slot name="error"></slot>
        `;
    }


    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

    get placeholder() {
        return this.getAttribute('placeholder');
    }

    get name() {
        return this.getAttribute('name');
    }

    get value() {
        return this.getAttribute('value') || '';
    }

    get isRounded() {
        return this.hasAttribute('rounded');
    }

    get hasIconLeft() {
        return this.querySelector(`*[slot='icon-left']`) !== null;
    }

    get hasIconRight() {
        return this.querySelector(`*[slot='icon-right']`) !== null;
    }
}