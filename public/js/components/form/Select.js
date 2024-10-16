import {WebComponent} from "../WebComponent.js";

export class Select extends WebComponent {

    static get observedAttributes() {
        return []
    }

    constructor() {
        super();

        this.input = this.shadow.querySelector('input');
        // this.input.name = this.getAttribute('name') ?? '';

        this.trigger = this.shadow.querySelector('.trigger');
        this.trigger.innerHTML += `<i data-lucide="chevron-down"></i>`
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
            .select {
                width: 100%;
                position: relative;
            }
            
            .trigger {
                width: 100%;
                padding: .8rem 2rem;
                
                border: 1px solid rgb(var(--color-gray-2));
                border-radius: var(--radius-small);
                background: none;
                
                font-size: inherit;
                
                cursor: pointer;
            }
            
            .options {
                
            }
        </style>
        `;
    }

    render() {
        return `
            <div class="select">
                <input type="hidden">

                <button class="trigger">
                    <slot name="trigger"></slot>
                </button>
                
                <slot name="options"></slot>
            </div>
        `;
    }
}