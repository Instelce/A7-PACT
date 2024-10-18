import {useId} from "../utils/id.js";

/**
 * Abstract class for web components
 *
 * @extends HTMLElement
 */
export class WebComponent extends HTMLElement {

    /**
     * @type {{name: string, value: string}[]}
     */
    styleVariables = [];

    /**
     * Define observed attributes
     * @returns {*[]}
     */
    static get observedAttributes() {return []};

    constructor() {
        super();

        // Unique id for the component
        this.id = useId();

        this.shadow = this.attachShadow({mode: "open"});

        const style = document.createRange().createContextualFragment(this.styles());
        const node = document.createRange().createContextualFragment(this.render());
        this.shadow.append(style, node);

        this.#renderNoScope();
    }

    /**
     * Callback when the component is connected to the DOM
     */
    connectedCallback() {
        this.#loadStyleVariables();
    }

    /**
     * Callback when the component is disconnected from the DOM
     */
    disconnectedCallback() {}

    /**
     * Callback when an attribute is changed
     * @param name
     * @param oldValue
     * @param newValue
     */
    attributeChangedCallback(name, oldValue, newValue) {}

    /**
     * Style of the component
     * @return {string}
     */
    styles() {}

    /**
     * Render the component
     * @returns {string}
     */
    render() {}

    /**
     * Render outside shadow root the component
     *
     * @returns {string}
     */
    noScope() {}

    #renderNoScope() {
        this.innerHTML += this.noScope();
        // this.innerHTML += `<div slot="hidden">${this.noScope()}</div>`;
    }

    /**
     * Re-render the component
     */
    reRender() {
        // Remove all children
        let children = this.shadow.children;
        for (let i = 0; i < children.length; i++) {
            this.shadow.removeChild(children[i]);
        }

        // Add the new children
        const style = document.createRange().createContextualFragment(this.styles());
        const node = document.createRange().createContextualFragment(this.render());
        this.shadow.append(style, node);

        this.#renderNoScope();
    }

    /**
     * Add a style variable
     * @param {string} name
     * @param {string} value
     */
    addStyleVariable(name, value) {
        this.styleVariables.push({name, value});
    }

    /**
     * Generate the style variables
     * @returns {string}
     */
    #loadStyleVariables() {
        this.styleVariables.forEach(({name, value}) => {
            this.style.setProperty(`--${name}`, value);
        })
    }

    /**
     * Returns true if connected as a pro
     * @returns {boolean}
     */
    isPro() {
        return this.hasAttribute('pro');
    }
}