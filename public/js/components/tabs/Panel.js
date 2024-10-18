import {useId} from "../../utils/id.js";

export class Panel extends HTMLElement {

    constructor() {
        super();
    }

    connectedCallback() {
        this.setAttribute('role', 'tabpanel');

        if (!this.id) {
            this.id = `panel-${useId()}`;
        }
    }
}