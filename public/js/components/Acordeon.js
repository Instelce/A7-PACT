/// Template of web component
/// Use this template to create a new web component
/// Watch the video in the documentation to create a new template file in intellij / phpstorm.

/// Composant call
///<x-acordeon text="Acordeon">
///    <div slot="content">
///        <p>Bravo vous avez réussie a ouvrir l'accordeon ! maintenant vous pouvez le fermez.</p>
///    </div>
///</x-acordeon>

import { WebComponent } from "./WebComponent.js";
/**
 * Input component
 *
 * @arg {string} text - Title of the acordeon
 * @arg {string} content - Title of the acordeon

 *
 * @extends {WebComponent}
 */
export class Acordeon extends WebComponent {

    static get observedAttributes() { return [] }

    constructor() {
        super();
        let topPart = this.shadowRoot.querySelector("#topPart");
        let content = this.shadowRoot.querySelector("#content");
        let arrow = this.shadowRoot.querySelector("#arrow");


        topPart.addEventListener('click', function () {
            arrow.classList.toggle('open');
            content.classList.toggle('content-hidden');
            content.classList.toggle('content-visible');
        });
    }

    connectedCallback() {
        super.connectedCallback();
    }

    disconnectedCallback() {
        super.connectedCallback();
    }

    attributeChangedCallback(name, oldValue, newValue) { }

    styles() {
        return `
        <style>
            .acordeon {
                width: 100%;
                position: relative;
                padding: 0 0.5rem;
                border-top: 1px solid rgb(var(--color-gray-2));
            }
            .titre {
                width: 100%;
                padding: 0.5rem 0;
                background: none;
                font-size: inherit;
                font-weight: bold;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                flex-direction: row;
                align-items: center;
            }
            .arrow {
                float: right;
                transition: all 0.5s;
                transform: rotate(0deg);
            }
            .open {
                transform: rotate(180deg);
            }
            .content {
                overflow: hidden;
                transition: all 0.5s;
                width: 100%;
            }
            .content-hidden {
                padding: 0;
                height: 0;
            }
            .content-visible {
                padding: 0.5rem 0;
                height: auto;
            }
        </style>
        `;
    }

    render() {
        return `
        <div class="acordeon">
            <div class="titre" id="topPart">
                <p>${this.text}</p>
                <div class="arrow" id="arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
            <div class="content content-hidden" id="content">
                <slot name="content"></slot>
            </div>
        </div>`;
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //


    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //
    get text() {
        return this.getAttribute('text');
    }

}