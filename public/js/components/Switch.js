import { WebComponent } from './WebComponent.js';

/**
 * Input component
 *
 * @arg {string} placeholder - Placeholder text for the input field.
 * @arg {boolean} hasbutton - Determines if the input field has an associated button.
 * @arg {boolean} rounded - Determines if the input field has rounded corners.
 * @arg {string} txtButton - Text to display on the button if `hasbutton` is true.
 *
 * @extends {WebComponent}
 */
export class Switch extends WebComponent {

    constructor() {
        super();
    }

    styles(){
        const rounded = this.getAttribute('rounded') === 'true' ? '200px' : '5px';
        return `
            <style>
                input[type=checkbox]{
                    \theight: 0;
                    \twidth: 0;
                    \tvisibility: hidden;
                }
                    
                label {
                    \tcursor: pointer;
                    \ttext-indent: -9999px;
                    \twidth: 200px;
                    \theight: 100px;
                    \tbackground: grey;
                    \tdisplay: block;
                    \tborder-radius: 100px;
                    \tposition: relative;
                }
                    
                label:after {
                    \tcontent: '';
                    \tposition: absolute;
                    \ttop: 5px;
                    \tleft: 5px;
                    \twidth: 90px;
                    \theight: 90px;
                    \tbackground: #fff;
                    \tborder-radius: 90px;
                    \ttransition: 0.3s;
                }
                    
                input:checked + label {
                    \tbackground: #bada55;
                }
                    
                input:checked + label:after {
                    \tleft: calc(100% - 5px);
                    \ttransform: translateX(-100%);
                }
                    
                    // centering
                body {
                    \tdisplay: flex;
                    \tjustify-content: center;
                    \talign-items: center;
                    \theight: 100vh;
                }
            </style>
        `;
    }

    render() {
        return `
            <div>
                <input type="checkbox" placeholder="" />
                <label for=""><slot></slot></label>
            </div>
        `;
    }
}