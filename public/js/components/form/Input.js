import { WebComponent } from '../WebComponent.js';

/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * @arg {string} name - Name of the input field.
 * @arg {boolean} rounded - Toggle rounded corners for the input field.
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

        let input = this.shadow.querySelector('input');
        input.setAttribute('placeholder', this.placeholder);
        input.setAttribute('name', this.name);
        input.setAttribute('value', this.value);

        let field = this.shadow.querySelector('.input-field');
        field.addEventListener('click', () => {
            input.focus();
        })
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
                
                .input-field:has(input:focus) {
                    box-shadow: 0 0 0 1px rgb(var(--color-gray-2));
                }
                
                input {
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
                
                <input type="text" />
                
                <slot name="button"></slot>
                
                ${this.hasIconRight ? `<div class="icon-right">
                    <slot name="icon-right"></slot>   
                </div>` : ''}
            </div>
            
            <slot name="helper"></slot>
            <slot name="error"></slot>
        `;
    }

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