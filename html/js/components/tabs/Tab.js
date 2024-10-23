import {useId} from "../../utils/id.js";

/**
 * Tab component
 */
export class Tab extends HTMLElement {

    static get observedAttributes() {
        return ['selected']
    }

    constructor() {
        super();
    }

    connectedCallback() {
        this.setAttribute('role', 'tab');

        this.setAttribute('aria-selected', 'false');
        this.setAttribute('tabindex', '-1');
        this.upgradeProperty('selected');
    }

    attributeChangedCallback(name, oldValue, newValue) {
        const selected = this.hasAttribute('selected');
        this.setAttribute('aria-selected', selected);
        this.setAttribute('tabindex', selected ? '0' : '-1');
    }


    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

    set selected(value) {
        if (value) {
            this.setAttribute('selected', '');
        } else {
            this.removeAttribute('selected');
        }
    }

    get selected() {
        return this.hasAttribute('selected');
    }

    // ---------------------------------------------------------------------- //
    // Other functions
    // ---------------------------------------------------------------------- //

    upgradeProperty(prop) {
        if (this.hasOwnProperty(prop)) {
            let value = this[prop];
            delete this[prop];
            this[prop] = value
        }
    }
}