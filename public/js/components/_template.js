/// Template of web component
/// Use this template to create a new web component
/// Watch the video in the documentation to create a new template file in intellij / phpstorm.

import {WebComponent} from "./WebComponent";

export class Template extends WebComponent {

    static get observedAttributes() { return [] }

    constructor() {
        super();
    }

    connectedCallback() {}

    disconnectedCallback() {}

    attributeChangedCallback (name, oldValue, newValue) {}

    style() {
        return `
        <style>
        
        </style>
        `;
    }

    render() {
        return ``;
    }
}