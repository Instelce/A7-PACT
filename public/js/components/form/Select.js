import {WebComponent} from "../WebComponent.js";

export class Select extends WebComponent {

    constructor() {
        super();

        this.input = this.querySelector('input');
        this.input.setAttribute('name', this.getAttribute('name'));
        this.input.setAttribute('value', this.getAttribute('value'));

        this.trigger = this.shadow.querySelector('.trigger');
        this.chevron = this.shadow.querySelector('.chevron');
        this.optionsContainer = this.querySelector('[slot="options"]');
        this.optionsButton = this.querySelectorAll('[slot="options"] > *');
        this.triggerValue = this.querySelector('[slot="trigger"]');

        // Open the options when the trigger is clicked
        this.trigger.addEventListener('click', () => {
            this.optionsContainer.classList.toggle('open');
            this.chevron.classList.toggle('rotate');
        });

        // Set the role of the options container
        this.optionsContainer.setAttribute('role', 'listbox');
        this.trigger.setAttribute('aria-haspopup', 'listbox');

        this.optionsButton.forEach(option => {
            // Set role
            option.setAttribute('role', 'option');

            // Set the selected option at the beginning
            if (option.getAttribute('data-value') === this.input.value) {
                this.triggerValue.innerHTML = option.textContent;
                option.classList.add('selected');
            }
        });

        // Add the check icon to the options
        this.optionsButton.forEach(option => {
            let check = document.createElement('div');
            check.classList.add('check');
            check.classList.add('opacity-0');
            check.innerHTML = this.renderCheck();
            option.appendChild(check);
        })

        // Set the selected option when clicked
        this.optionsButton.forEach(option => {
            let check = option.querySelector('.check');
            option.addEventListener('click', () => {
                if (!option.classList.contains('selected')) {
                    this.input.value = option.getAttribute('data-value');
                    this.triggerValue.innerHTML = option.textContent;
                    this.querySelector('[slot="options"]').classList.remove('open');
                    this.chevron.classList.remove('rotate');

                    this.optionsButton.forEach(option => {
                        let check = option.querySelector('.check');
                        check.classList.add('opacity-0');
                        option.classList.remove('selected');
                    })

                    check.classList.remove('opacity-0');
                    option.classList.add('selected');
                }
            })
        })

        // Click outside the select to close the options
        document.addEventListener('click', (event) => {
            if (!this.contains(event.target)) {
                this.optionsContainer.classList.remove('open');
                this.chevron.classList.remove('rotate');
            }
        })
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
                min-width: 200px;
                position: relative;
            }
            
            .trigger {
                width: 100%;
                padding: .8rem 1.5rem;
                
                border: 1px solid rgb(var(--color-gray-2));
                border-radius: var(--radius-small);
                background: none;
                
                font-size: inherit;
                
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                
                cursor: pointer;
            }
            
            ::slotted([slot="options"]) {
                width: 100%;
                top: calc(100% + 1rem);
                left: 0;
                position: absolute;
                border: 1px solid rgb(var(--color-gray-2));
                border-radius: var(--radius-small);
                background: white;
                z-index: 100;
                overflow: hidden;
                display: none;
                flex-direction: column;
            }
            
            ::slotted([slot="options"].open) {
                display:flex;
            }
            
            .chevron {
                transition: transform .1s;
            }
            
            .chevron.rotate {
                transform: rotate(180deg);
            }
        </style>
        `;
    }

    render() {
        return `
            <div class="select">
                <button class="trigger">
                    <slot name="trigger"></slot>
                    ${this.renderChevron()}
                </button>
                
                <slot name="options"></slot>
            </div>
        `;
    }

    noScope() {
        return `
            <input type="hidden">
        `
    }

    renderChevron() {
        return `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down chevron"><path d="m6 9 6 6 6-6"/></svg>
        `;
    }

    renderCheck() {
        return `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>
        `
    }
}