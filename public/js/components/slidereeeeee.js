import { WebComponent } from "./WebComponent.js";

/**
 * Slider component
 *
 * @arg {string} color - Color of the slider. Default is blue
 */
export class Slider extends WebComponent {
    static get observedAttributes() {
        return ["label", "min", "max", "type", "color"];
    }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.updateRender();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this.updateRender();
        }
    }

    styles() {
        let color = this.getAttribute("color") || '#0057FF';
        return `
            <style>
                .container {
                    width: 200px;
                    
                }
                .labels {
                    display: flex;
                    justify-content: space-between;
                    font-size: 14px;
                   
                }
                [slider] {
                    position: relative;
                    height: 14px;
                    border-radius: 10px;
                    text-align: left;
                    margin: 45px 0 10px 0;
                }
                [slider] > div {
                    position: absolute;
                    left: 13px;
                    right: 15px;
                    height: 14px;
                }
                [slider] > div > [inverse-left],
                [slider] > div > [inverse-right] {
                    position: absolute;
                    height: 8px;
                    border-radius: 100px;
                    background-color: #CCC;
                }
                [slider] > div > [range] {
                    position: absolute;
                    left: 0;
                    height: 8px;
                    border-radius: 100px;
                    background-color: var(--slider-color, ${color});
                }
                [slider] > div > [thumb] {
                    position: absolute;
                    
                    z-index: 2;
                    height: 15px;
                    width: 15px;
                    margin-left: -11px;
                    margin-top: -4px;
                    cursor: pointer;
                    background-color: #FFF;
                    border: 2.5px solid ${color};  
                    border-radius: 100%;
                    box-sizing: border-box;
                    
                }
                [slider] > input[type=range] {
                    position: absolute;
                    pointer-events: none;
                    z-index: 3;
                    height: 14px;
                    top: -2px;
                    width: 100%;
                    opacity: 0;
                }
            </style>
        `;
    }

    render() {
        if (this.getAttribute("type") === "minmax") {
            // Render the min-max slider
            return `
                <div class="container">
                    <div class="labels">
                        <span>${this.getAttribute("label") || 'Label'}</span>
                        <span>Min-Max</span>
                    </div>
                    <div slider id="slider-distance">
                        <div>
                            <div inverse-left style="width:70%;"></div>
                            <div inverse-right style="width:70%;"></div>
                            <div range style="left:30%;right:40%;"></div>
                            <span thumb style="left:30%;"></span>
                            <span thumb style="left:60%;"></span>
                        </div>
                        <input type="range" id="minRange" value="30" max="100" min="0" step="1" oninput="this.parentNode.querySelector('[range]').style.left = this.value + '%';" />
                        <input type="range" id="maxRange" value="60" max="100" min="0" step="1" oninput="this.parentNode.querySelector('[range]').style.right = (100 - this.value) + '%';" />
                    </div>
                </div>
            `;
        } else {
            // Render the basic slider
            return `
                <div class="container">
                    <div class="labels">
                        <span>${this.getAttribute("label") || 'Label'}</span>
                        <span>Value</span>
                    </div>
                    <div>
                        <input type="range" id="basicRange" value="50" min="${this.getAttribute("min") || 0}" max="${this.getAttribute("max") || 100}" step="1" style="--slider-color:${this.getAttribute("color") || '#1ABC9C'};">
                    </div>
                </div>
            `;
        }
    }

    updateRender() {
        this.shadowRoot.innerHTML = this.styles() + this.render();
        this.initializeSliders();  // Initialize the sliders
    }

    initializeSliders() {
        // Min-Max Slider Handling
        const minRange = this.shadowRoot.querySelector("#minRange");
        const maxRange = this.shadowRoot.querySelector("#maxRange");
        if (minRange && maxRange) {
            minRange.addEventListener('input', (e) => {
                if (parseInt(minRange.value) >= parseInt(maxRange.value)) {
                    minRange.value = maxRange.value - 1;
                }
            });
            maxRange.addEventListener('input', (e) => {
                if (parseInt(maxRange.value) <= parseInt(minRange.value)) {
                    maxRange.value = minRange.value + 1;
                }
            });
        }
    }
}
