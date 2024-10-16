import { WebComponent } from './WebComponent.js';

// Importer Lucide
import 'https://unpkg.com/lucide@latest/dist/umd/lucide.js';

/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * @arg {string} icon - Name of the icon to display.
 * @arg {string} leftorright - Position of the icon ('left' or 'right').
 */
export class InputButton extends WebComponent {
    static get observedAttributes() {
        return ['placeholder', 'icon', 'leftorright'];
    }

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
        lucide.createIcons(); 
    }

    disconnectedCallback() {
    }

    attributeChangedCallback(oldValue, newValue) {
        if (oldValue !== newValue) {
            this.updateRender();
        }
    }

    styles() {
        return `
            <style>
                div.wrapper {
                    display: flex;
                    align-items: center;
                    border: 1px solid #ccc;
                    border-radius: 200px;
                    padding: 10px 20px;
                    width: 160px;
                    height: 20px;
                    margin: 5px;
                    text-color: #838383;

                }
                input {
                    border: none;
                    outline: none;
                    flex: 1;
                    padding: 0;
                }

            </style>
        `;
    }

    render() {
        const placeholder = this.getAttribute('placeholder') || '';
        const iconName = this.getAttribute('icon') || '';
        const iconPosition = this.getAttribute('leftorright') || 'left';

        
        const iconHTML = iconName ? `<i data-lucide="${iconName}"></i>` : '';
        const inputHTML = `<input type="text" placeholder="${placeholder}" />`;

        if (iconPosition === 'left') {
            return `
                <div class="wrapper">
                    ${iconHTML}
                    ${inputHTML}
                </div>
            `;
        } else {
            return `
                <div class="wrapper">
                    ${inputHTML}
                    ${iconHTML}
                </div>
            `;
        }
    }

    updateRender() {
        this.shadow.innerHTML = this.styles() + this.render();
        lucide.createIcons(); 
    }
}

window.customElements.define('app-input', InputButton);
