import { WebComponent } from "../WebComponent.js";

/**
 * Slider component
 *
 * @extends {WebComponent}
 */
export class Slider extends WebComponent {
    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
        this.renderComponent();
        this.addRangeEventListeners();
    }

    disconnectedCallback() {}

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this[name] = newValue;
            this.reRender();
        }
    }

    // ---------------------------------------------------------------------- //
    // Styles and Rendering
    // ---------------------------------------------------------------------- //

    styles() {
        return `
        <style>
            .slider-container {
                display: flex;
                flex-direction: column;
                width: 300px;
                margin: 100px;
                position: relative;
            }
            .labels {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 14px;
               
            }
            .value-label {
                display: flex;
                justify-content: end;
                width: 80px;
                gap: 2px;
            }
            .slider#minSlider {
                position:absolute; ;
                margin-top: 23px;
                -webkit-appearance: none;
                width: 100%;
                height: 8px;
                background: #E1E1E1;
                border-radius: 100px;
                cursor: pointer;
                z-index: 1;
            }
            .slider#maxSlider {
                position: absolute;
                margin-top: 23px;
                -webkit-appearance: none;
                width: 100%;
                height: 8px;
                background: #E1E1E1;
                border-radius: 100px;
                cursor: pointer;
                z-index: 1;
    }
            .slider::-webkit-slider-thumb {
                position : relative;
                -webkit-appearance: none;
                width: 16px;
                height: 16px;
                border-radius: 100%;
                background: white;
                border: 2px solid ${this.color};
                cursor: pointer;
                
                margin-left: -2px;
                z-index: 7;
            }
            .range {
                position: relative;
                height: 8.5px;
                background-color: ${this.color};
                border-radius: 5px;
                top: 6px;
                z-index: 1;
            }
        </style>
        `;
    }

    render() {
        if (this.type.toLowerCase() === "minmax") {
            return `
<div class="slider-container">
    <div class="labels">
        <label>${this.label}</label>
        <div class="value-label">
            <span id="minValue">${this.min}</span>
            <span> - </span>
            <span id="maxValue">${this.max}</span>
        </div>
    </div>
    <input type="range" min="${this.min}" max="${this.max}" value="${
                this.min
            }" class="slider"
           id="minSlider">
    <input type="range" min="${this.min}" max="${this.max}" value="${
                this.max
            }" class="slider"
           id="maxSlider">
    <div class="thumb" id="minThumb" style="left: ${this.getPercentage(
        this.min
    )}%;"></div>
    <div class="thumb" id="maxThumb" style="left: ${this.getPercentage(
        this.max
    )}%;"></div>
    <div class="range" id="range"></div>

</div>
            `;
        } else {
            return `
            <div class="slider-container">
                <div class="labels">
                    <label>${this.label}</label>
                    <div class="value-label">
                        <span id="value">this.value</span>
                    </div>
                </div>
                <input type="range" min="${this.min}" max="${
                this.max
            }" value="${this.min}" class="slider" id="minSlider">
                <div class="range" id="range"></div>    
                <div class="thumb" id="minThumb" style="left: ${this.getPercentage(
                    this.min
                )}%;"></div>

            </div>
            `;
        }
    }

    renderComponent() {
        this.shadow.innerHTML = this.styles() + this.render();
        this.updateTrack();
    }

    // ---------------------------------------------------------------------- //
    // Utility Functions
    // ---------------------------------------------------------------------- //

    getPercentage(value) {
        return ((value - this.min) / (this.max - this.min)) * 100;
    }

    updateTrack() {
        const minSlider = this.shadow.querySelector("#minSlider");
        const maxSlider = this.shadow.querySelector("#maxSlider");
        const range = this.shadow.querySelector("#range");
        const minThumb = this.shadow.querySelector("#minThumb");
        const maxThumb = this.shadow.querySelector("#maxThumb");

        const minValue = parseInt(minSlider.value);
        const maxValue = parseInt(maxSlider.value);

        const minPercent = this.getPercentage(minValue);
        const maxPercent = this.getPercentage(maxValue);

        range.style.left = `${minPercent}%`;
        range.style.width = `${maxPercent - minPercent}%`;

        minThumb.style.left = `${minPercent}%`;
        maxThumb.style.left = `${maxPercent}%`;

        this.shadow.querySelector("#minValue").textContent = minValue;
        this.shadow.querySelector("#maxValue").textContent = maxValue;
    }

    addRangeEventListeners() {
        const minSlider = this.shadow.querySelector("#minSlider");
        const maxSlider = this.shadow.querySelector("#maxSlider");

        minSlider.addEventListener("input", () => {
            if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
                minSlider.value = maxSlider.value;
            }
            this.updateTrack();
        });

        maxSlider.addEventListener("input", () => {
            if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
                maxSlider.value = minSlider.value;
            }
            this.updateTrack();
        });
    }

    // ---------------------------------------------------------------------- //
    // Getters and Setters
    // ---------------------------------------------------------------------- //

    get type() {
        return this.getAttribute("type");
    }
    set type(value) {
        this.setAttribute("type", value);
    }
    get label() {
        return this.getAttribute("label") || "Label";
    }
    set label(value) {
        this.setAttribute("label", value);
    }
    get min() {
        return parseInt(this.getAttribute("min"));
    }
    set min(value) {
        this.setAttribute("min", value);
    }
    get max() {
        return parseInt(this.getAttribute("max"));
    }
    set max(value) {
        this.setAttribute("max", value);
    }
    get color() {
        return this.getAttribute("color") || "blue";
    }
    set color(value) {
        this.setAttribute("color", value);
    }

    reRender() {
        this.renderComponent();
        this.addRangeEventListeners();
    }
}
