import { WebComponent } from './WebComponent.js';

// Importer Lucide
import 'https://unpkg.com/lucide@latest/dist/umd/lucide.js';

class InputButton extends WebComponent {
    static get observedAttributes() {
        return ['placeholder', 'icon', 'leftorright'];
    }

    constructor() {
        super();
    }

    style() {
        return `
            <style>
                div.wrapper {
                    display: flex;
                    align-items: center;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    padding: 5px;
                    width: 200px;
                    height: 40px;
                }
                input {
                    border: none;
                    outline: none;
                    flex: 1;
                }
                div.icon-wrapper {
                    display: flex;
                    align-items: center;
                    margin: 0 5px;
                }
            </style>
        `;
    }

    render() {
        const placeholder = this.getAttribute('placeholder') || '';
        const iconName = this.getAttribute('icon') || '';
        const iconPosition = this.getAttribute('leftorright') || 'left';

        // Utiliser data-lucide pour les icônes
        const iconHTML = iconName ? `<div class="icon-wrapper"><i data-lucide="${iconName}"></i></div>` : '';
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

    connectedCallback() {
        // Appeler lucide.createIcons() pour rendre les icônes
        lucide.createIcons();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this.shadow.innerHTML = this.render();
            lucide.createIcons(); // Rendre les icônes à chaque changement d'attribut
        }
    }
}

// Définir le custom element
window.customElements.define('app-input', InputButton);
