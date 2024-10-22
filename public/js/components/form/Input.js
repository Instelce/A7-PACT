import {WebComponent} from '../WebComponent.js';

/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * @arg {string} name - Name of the input field.
 * @arg {boolean} rounded - ToggleSwitch rounded corners for the input field.
 * @arg {string} value - Value of the input
 *
 * Slots
 * - label: Label for the input field.
 * - input: Input field.
 * - icon-left: Icon on the left side of the input field.
 * - icon-right: Icon on the right side of the input field.
 *
 * @extends {WebComponent}
 */
export class Input extends WebComponent {

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();

        let label = this.shadow.querySelector('label');
        let input = this.querySelector('[slot="input"]');
        let field = this.shadow.querySelector('.input-field');
        let required = input.hasAttribute('required');

        if (input.id) {
            label.setAttribute('for', input.id);
        }
        if (required) {
            label.classList.add('required');
        }

        // Focus input when clicking on the field
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

        if (this.hasButton) {
            field.classList.add('has-button')
        }

        // Add autocomplete list
        let list = this.querySelector('[slot="list"]');
        list.role = 'listbox';

        if (list) {
            let listOption = list.querySelectorAll('[slot="list"] > *');
            this.listOptions = [];
            this.currentIndex = -1;

            // Retrieve all options text
            listOption.forEach(option => {
                this.listOptions.push(option.textContent);
                option.classList.add('option');
            });

            input.addEventListener('input', (e) => {
                // Show the list when typing
                let value = e.target.value;
                if (value.length > 0) {
                    this.currentIndex = 0;
                    list.classList.add('open');
                } else {
                    this.currentIndex = -1;
                    list.classList.remove('open');
                }

                // Remove all options
                list.innerHTML = '';

                // Add options that match the input value
                this.listOptions.forEach((option, index) => {
                    if (option.toLowerCase().includes(value.toLowerCase())) {
                        let el = document.createElement('div');
                        el.classList.add('option');
                        el.textContent = option;
                        list.appendChild(el);

                        el.addEventListener('click', () => {
                            input.value = el.textContent;
                            list.classList.remove('open');
                        });

                        if (index === this.currentIndex) {
                            el.classList.add('selected');
                        }
                    }
                });

                if (list.children.length === 0) {
                    this.currentIndex = -1;
                    list.classList.remove('open');
                }
            });

            input.addEventListener('keydown', (e) => {
                let options = list.querySelectorAll('.option');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.currentIndex = (this.currentIndex + 1) % options.length;
                    options.forEach((option, index) => {
                        option.classList.toggle('selected', index === this.currentIndex);
                    });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.currentIndex = (this.currentIndex - 1 + options.length) % options.length;
                    options.forEach((option, index) => {
                        option.classList.toggle('selected', index === this.currentIndex);
                    });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.currentIndex >= 0 && this.currentIndex < options.length) {
                        input.value = options[this.currentIndex].textContent;
                        list.classList.remove('open');
                    }
                }
            });
        }
    }

    disconnectedCallback() {
    }

    styles() {
        const rounded = this.isRounded ? 'var(--radius-rounded)' : 'var(--radius-small)';
        return `
            <style>
                :host {
                    width: 100%;
                    position:relative;
                }
                .input-container {
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    gap: .5rem;
                }
                
                .input-field {
                    width: 100%;
                    padding: .8rem 1rem;
                    box-sizing: border-box;
                    
                    display: flex;
                    gap: .5rem;
                    align-items: center;
                    
                    border: 1px solid #ccc;
                    border-radius: ${rounded};
                    color: #838383;
                    
                    transition: box-shadow .2s;
                }
                
                .input-field.focused {
                    box-shadow: 0 0 0 1px rgb(var(--color-gray-2));
                }
                
                .input-field.has-button {
                    padding: .3rem .3rem;
                    padding-left: 1rem;
                }
                
                .input:has(.icon-left) {
                    padding-left: .5rem;
                }
                
                .input:has(.icon-right) {
                    padding-right: .5rem;
                }
                
                ::slotted([slot="input"]) {
                    height: 100%;
                    width: 100%;
                    border: none;
                    outline: none;
                    -webkit-appearance: none;
                    appearance: none;
                    
                    font-family: inherit;
                    font-size: inherit;
                    color: black;
                }
                
                ::slotted([slot="input"])::placeholder {
                  font-style: italic;
                  color: rgb(var(--color-gray-3));
                }
                
                .icon-left, .icon-right {
                  color: rgb(var(--color-black));
                }
                
                label {
                   display: flex;
                   align-items: center;
                   gap: .5rem;
                }
                
                label svg {
                    display: none;
                    color: rgb(var(--color-gray-4));
                }
                
                label.required svg {
                  display: inline-block;
                }

                .helper {
                  color: rgb(var(--color-gray-3));
                  margin-top: .2rem;
                  font-size: var(--typescale-d1);
                }
                
                .error {
                    margin-top: .5rem;
                    margin-bottom: .5rem;
                    color: rgb(var(--color-danger));
                    font-weight: 500;
                }
                
                :host([data-invalid]) .input-field {
                  border: 1px solid rgb(var(--color-danger));
                }
                
                /* Autocomplete list */
                ::slotted([slot="list"]) {
                    width: 100%;
                    top: calc(100% + 1rem);
                    left: 0;
                    position: absolute;
                    border: 1px solid rgb(var(--color-gray-2));
                    border-radius: var(--radius-small);
                    background: white;
                    z-index: 100;
                    overflow: hidden;
                    display:flex;
                    flex-direction: column;
                    pointer-events: none;
                    opacity: 0;
                    transform: translateY(-.5rem);
                    transition: opacity .1s, transform .1s;
                }
                
                ::slotted([slot="list"].open) {
                    opacity: 1;
                    transform: translateY(0);
                    pointer-events: auto;
                }
            </style>
        `;
    }

    render() {
        return `
            <div class="input-container">
                <label>
                    <slot name="label"></slot>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-asterisk"><path d="M12 6v12"/><path d="M17.196 9 6.804 15"/><path d="m6.804 9 10.392 6"/></svg>
                </label>
                <div class="input-field">
                    ${this.hasIconLeft ? `<div class="icon-left">
                        <slot name="icon-left"></slot>   
                    </div>` : ''}
                    
                    <slot name="input"></slot>
                    <slot name="button"></slot>
                    
                    ${this.hasIconRight ? `<div class="icon-right">
                        <slot name="icon-right"></slot>   
                    </div>` : ''}
                    
                    <!-- Autocomplete list -->
                    <slot name="list"></slot>
                </div>
            </div>
            
            <div class="helper"><slot name="helper"></slot></div>
            <div class="error"><slot name="error"></slot></div>
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

    get hasButton() {
        return this.querySelector(`*[slot='button']`) !== null;
    }
}