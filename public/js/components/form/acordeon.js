/// Template of web component
/// Use this template to create a new web component
/// Watch the video in the documentation to create a new template file in intellij / phpstorm.

import { WebComponent } from "./WebComponent.js";

export class Template extends WebComponent {

    static get observedAttributes() { return [] }

    constructor() {
        super();
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
        
        </style>
        `;
    }

    render() {
        return `
        <div>
        <h4></h4>
            <slot></slot>
        </div>`;
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //


    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

}