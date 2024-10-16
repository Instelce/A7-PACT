/**
 * Abstract class for web components
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

        this.shadow = this.attachShadow({mode: "open"});

        const style = document.createRange().createContextualFragment(this.styles());
        const node = document.createRange().createContextualFragment(this.render());
        this.shadow.append(style, node);
    }

    /**
     * Callback when the component is connected to the DOM
     */
    connectedCallback() {
        this.loadStyleVariables();
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
    attributeChangedCallback (name, oldValue, newValue) {}

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
     * Re-render the component
     */
    reRender() {
        this.shadow.removeChild(this.shadow.childNodes);
        const style = document.createRange().createContextualFragment(this.styles());
        const node = document.createRange().createContextualFragment(this.render());
        this.shadow.append(style, node);
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
    loadStyleVariables() {
        this.styleVariables.forEach(({name, value}) => {
            this.style.setProperty(`--${name}`, value);
        })
    }

    /**
     * Returns true if connected as a pro
     * @returns {boolean}
     */
    isPro()
    {
        return this.hasAttribute('pro');
    }
}