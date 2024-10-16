import {WebComponent} from "./WebComponent.js";


/**
 * Button component
 *
 * @arg {string} type - Type of the button. Default is button
 * @arg {string} color - Color of the button. Default is blue
 * @arg {string} size - Size of the button. Default is medium (md)
 * @arg {boolean} icon - Only show the icon
 *
 */
export class Button extends WebComponent {

    static get observedAttributes() {
        return []
    }

    constructor() {
        super();

        let button = this.shadow.querySelector('button');
        button.classList.add(this.color, this.size);
        button.setAttribute('type', this.type);

        if (this.hasIconLeft) {
            button.classList.add('icon-left');
        } else if (this.hasIconRight) {
            button.classList.add('icon-right');
        }

        if (this.onlyIcon) {
            button.classList.add('only-icon');
        }
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
            
                    background: rgb(var(--background));
                    border: 1px solid rgb(var(--border));
                    border-radius: var(--radius-rounded);
                    outline: none;
            
                    font-size: inherit;
                    font-weight: 500;
                    white-space: nowrap;
                    color: rgb(var(--color));
            
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 1rem;
            
                    cursor: pointer;
                    transition: color .2s, background .2s, border .2s, box-shadow .2s;
                }
                
                button:hover {
                    background: rgb(var(--background-hover));
                    border: 1px solid rgb(var(--border-hover));
                    color: rgb(var(--color-hover));
                }
                
                button:focus {
                  box-shadow: 0 0 0 3px rgba(var(--background), .5);
                  transform: scale(1.02);
                }
            
                button.blue {
                    --background: var(--color-blue-primary);
                    --border: var(--color-blue-primary);
                    --color: var(--color-white);
                    --background-hover: var(--color-white);
                    --border-hover: var(--color-blue-primary);
                    --color-hover: var(--color-blue-primary);
                }
            
                button.gray {
                    --background: var(--color-white);
                    --border: var(--color-gray-2);
                    --color: var(--color-black);
                    --background-hover: var(--color-gray-1);
                    --border-hover: var(--color-gray-2);
                    --color-hover: var(--color-black);
                }
            
                button.danger {
                    --background: var(--color-danger);
                    --border: var(--color-danger);
                    --color: var(--color-white);
                    --background-hover: var(--color-white);
                    --border-hover: var(--color-danger);
                    --color-hover: var(--color-danger);
                }
            
                button.purple {
                    --background: var(--color-purple-primary);
                    --border: var(--color-purple-primary);
                    --color: var(--color-white);
                    --background-hover: var(--color-white);
                    --border-hover: var(--color-purple-primary);
                    --color-hover: var(--color-purple-primary);
                }

                button.sm {
                    padding: .5rem 1.5rem;
                }
            
                button.md {
                    padding: .8rem 2rem;
                }
            
                button.lg {
                    padding: 1rem 2.5rem;
                }
                
                button.only-icon {
                    padding: 0;
                }
                
                button.only-icon.sm {
                    width: 2.5rem;
                    height: 2.5rem;
                }
                
                button.only-icon.md {
                    width: 3rem;
                    height: 3rem;
                }
                
                button.only-icon.lg {
                    width: 3.5rem;
                    height: 3.5rem;
                }
                
                ::slotted(svg) {
                    width: 1.2rem;
                    height: 1.2rem;
                }

                button.icon-left {
                    padding-left: 1rem;
                }
                
                button.icon-right {
                    padding-right: 1rem;
                }
                
                ::slotted([slot="icon-left"]),
                ::slotted([slot="icon-right"]) {
                    width: 1.2rem;
                    height: 1.2rem;
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

    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

    get type() {
        return this.getAttribute('type') ?? "button";
    }

    /**
     * @returns {"gray"|"blue"|"purple"|"danger"}
     */
    get color() {
        return this.getAttribute('color') ?? "blue";
    }

    /**
     * @returns {"sm"|"md"|"lg"}
     */
    get size() {
        return this.getAttribute('size') ?? "md";
    }

    /**
     * @returns {boolean}
     */
    get onlyIcon() {
        return this.hasAttribute('icon');
    }

    get hasIconLeft() {
        return this.querySelector('[slot="icon-left"]') !== null;
    }

    get hasIconRight() {
        return this.querySelector('[slot="icon-right"]') !== null;
    }
}