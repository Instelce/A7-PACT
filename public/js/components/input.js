import { WebComponent } from './WebComponent.js';


/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * 
 */
export class Input extends WebComponent {
    static get observedAttributes() {
        return ['placeholder'];
    }

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
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
                div {
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
                    padding: 5px;
                    width: 100%;
                }

            </style>
        `;
    }

    render() {
        const placeholder = this.getAttribute('placeholder');
       
            return `
            <div >
              <slot name="icon-left">
              <slot>
              </slot>
              <input type="text" placeholder="${placeholder}" />
              <slot name="icon-right"></slot>
            </div>
            `;
    }

    updateRender() {
        this.shadow.innerHTML = this.styles() + this.render();
        
    }
}
