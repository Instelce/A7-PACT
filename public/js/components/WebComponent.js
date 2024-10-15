/**
 * Abstract class for web components
 */
export class WebComponent extends HTMLElement {

    /**
     * Define observed attributes
     * @returns {*[]}
     */
    static get observedAttributes() { return [] }

    constructor() {
        super();

        this.shadow = this.attachShadow({mode: "open"});

        const style = document.createRange().createContextualFragment(this.style());
        const node = document.createRange().createContextualFragment(this.render());
        this.shadow.append(style, node);
    }

    /**
     * Callback when the component is connected to the DOM
     */
    connectedCallback() {}

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
    attributeChangedCallback (name, oldValue, newValue) {}

    /**
     * Style of the component
     * @return {string}
     */
    style() {
        return ``;
    }

    /**
     * Render the component
     * @returns {string}
     */
    render() {
        return ``;
    }
}