import { WebComponent } from "../WebComponent.js";

/**
 * Slider component
 *
 * @extends {WebComponent}
 */
export class Slider extends WebComponent {
    constructor() {
        super();
        this.draggingHandle = null;
        this.mouseMoveHandler = null;
    }

    connectedCallback() {
        super.connectedCallback();
        this.renderComponent();
        this.addRangeEventListeners();
    }
    disconnectedCallback() { }

    attributeChangedCallback(name, oldValue, newValue) { }

    styles() {
        return `
        <style>
            .slider-container {
                display: flex;
                flex-direction: column;
                width: 150px;
                margin: 10px auto;
                position: relative;
            }
            .labels {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 14px;
                margin-bottom: 10px;
            }
            .range-slider {
                position: relative;
                display: flex;
                align-items: center;
                width: 100%;
                height: 8px;
                background-color: #E1E1E1;
                border-radius: 100px;
            }
            .range-slider-val-range {
                position: absolute;
                height: 100%;
                background-color: ${this.color};
                border-radius: 6px;
            }
            .range-slider-handle {
                position: absolute;
                width: 15px;
                height: 15px;
                background-color: #fff;
                border: 3px solid ${this.color};
                box-sizing: border-box;
                border-radius: 50%;
                cursor: pointer;
                top: -4px;
                margin-left: -7px;
            }
        </style>
        `;
    }

    render() {
        if (this.type === "double") {
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
                <div id="RangeSlider" class="range-slider">
                    <div class="range-slider-val-range"></div>

                    <span class="range-slider-handle range-slider-handle-left" tabindex="0"></span>
                    <span class="range-slider-handle range-slider-handle-right" tabindex="0"></span>
                </div>
                <input type="range" class="range-slider-input-left" min="${this.min}" max="${this.max}" value="${this.min}" style="display: none;">
                <input type="range" class="range-slider-input-right" min="${this.min}" max="${this.max}" value="${this.max}" style="display: none;">
            </div>
        `;
        } else {
            return `
            <div class="slider-container">
                <div class="labels">
                    <label>${this.label}</label>
                    <span id="minValue">${this.max}</span>
                </div>
                <div id="RangeSlider" class="range-slider">
                    <div class="range-slider-val-range"></div>
                    <span class="range-slider-handle range-slider-handle-left" tabindex="0"></span>
                </div>
                <input type="range" class="range-slider-input-left" min="${this.min}" max="${this.max}" value="${this.min}" style="display: none;">
            </div>
        `;
        }
    }

    renderComponent() {
        this.shadow.innerHTML = this.styles() + this.render();
        this.initializeSlider();
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //

    initializeSlider() {
        if (this.type === "double") {
            const rangeSlider = this.shadow.querySelector("#RangeSlider");
            const rangeSliderValRange = rangeSlider.querySelector(
                ".range-slider-val-range"
            );
            const rangeSliderHandleLeft = rangeSlider.querySelector(
                ".range-slider-handle-left"
            );
            const rangeSliderHandleRight = rangeSlider.querySelector(
                ".range-slider-handle-right"
            );

            const minValue = this.min;
            const maxValue = this.max;

            const leftPosition =
                ((minValue - this.min) / (this.max - this.min)) * 100;
            const rightPosition =
                ((maxValue - this.min) / (this.max - this.min)) * 100;

            rangeSliderHandleLeft.style.left = `${leftPosition}%`;
            rangeSliderHandleRight.style.left = `${rightPosition}%`;
            rangeSliderValRange.style.left = `${leftPosition}%`;
            rangeSliderValRange.style.width = `${rightPosition - leftPosition
                }%`;

            this.updateDisplayedValues(minValue, maxValue);
        } else {
            const rangeSlider = this.shadow.querySelector("#RangeSlider");
            const rangeSliderValRange = rangeSlider.querySelector(
                ".range-slider-val-range"
            );
            const rangeSliderHandleLeft = rangeSlider.querySelector(
                ".range-slider-handle-left"
            );

            const minValue = this.min;
            const maxValue = this.max;

            const leftPosition =
                ((minValue - this.min) / (this.max - this.min)) * 100;

            rangeSliderHandleLeft.style.left = `${leftPosition}%`;
            rangeSliderValRange.style.left = `0%`;
            rangeSliderValRange.style.width = `${leftPosition}%`;

            this.updateDisplayedValues(minValue);
        }
    }

    updateDisplayedValues(minValue, maxValue) {
        if (this.type === "double") {
            const minValueLabel = this.shadow.querySelector("#minValue");
            const maxValueLabel = this.shadow.querySelector("#maxValue");
            minValueLabel.innerText = minValue;
            maxValueLabel.innerText = maxValue;
        } else {
            const minValueLabel = this.shadow.querySelector("#minValue");
            minValueLabel.innerText = minValue;
        }
    }

    addRangeEventListeners() {
        if (this.type === "double") {
            const rangeSliderHandleLeft = this.shadow.querySelector(
                ".range-slider-handle-left"
            );
            const rangeSliderHandleRight = this.shadow.querySelector(
                ".range-slider-handle-right"
            );

            rangeSliderHandleLeft.addEventListener("mousedown", (e) =>
                this.handleMouseDown(e, "left")
            );
            rangeSliderHandleRight.addEventListener("mousedown", (e) =>
                this.handleMouseDown(e, "right")
            );
        } else {
            const rangeSliderHandleLeft = this.shadow.querySelector(
                ".range-slider-handle-left"
            );

            rangeSliderHandleLeft.addEventListener("mousedown", (e) =>
                this.handleMouseDown(e, "left")
            );
        }

        document.addEventListener("mouseup", () => this.stopDragging());
    }

    handleMouseDown(e, handle) {
        e.preventDefault();
        this.draggingHandle = handle;
        this.mouseMoveHandler = this.handleMouseMove.bind(this);
        document.addEventListener("mousemove", this.mouseMoveHandler);
    }

    handleMouseMove(e) {
        if (this.type === "double") {
            const rangeSlider = this.shadow.querySelector("#RangeSlider");
            const rangeSliderValRange = rangeSlider.querySelector(
                ".range-slider-val-range"
            );
            const rangeSliderHandleLeft = rangeSlider.querySelector(
                ".range-slider-handle-left"
            );
            const rangeSliderHandleRight = rangeSlider.querySelector(
                ".range-slider-handle-right"
            );
            const rangeSliderInputLeft = this.shadow.querySelector(
                ".range-slider-input-left"
            );
            const rangeSliderInputRight = this.shadow.querySelector(
                ".range-slider-input-right"
            );

            const rangeSliderRect = rangeSlider.getBoundingClientRect();
            const minValue = parseInt(rangeSliderInputLeft.value);
            const maxValue = parseInt(rangeSliderInputRight.value);

            let newValue =
                ((e.clientX - rangeSliderRect.left) / rangeSliderRect.width) *
                100;
            newValue = Math.max(0, Math.min(newValue, 100));

            const scaledValue = Math.round(
                this.min + (newValue / 100) * (this.max - this.min)
            );

            if (this.draggingHandle === "left") {
                if (scaledValue < maxValue) {
                    const leftPosition =
                        ((scaledValue - this.min) / (this.max - this.min)) *
                        100;
                    rangeSliderHandleLeft.style.left = `${leftPosition}%`;
                    rangeSliderValRange.style.left = `${leftPosition}%`;
                    rangeSliderValRange.style.width = `${((maxValue - scaledValue) / (this.max - this.min)) * 100
                        }%`;
                    rangeSliderInputLeft.value = scaledValue;
                    this.updateDisplayedValues(scaledValue, maxValue);
                }
            } else if (this.draggingHandle === "right") {
                if (scaledValue > minValue) {
                    const rightPosition =
                        ((scaledValue - this.min) / (this.max - this.min)) *
                        100;
                    rangeSliderHandleRight.style.left = `${rightPosition}%`;
                    rangeSliderValRange.style.width = `${((scaledValue - minValue) / (this.max - this.min)) * 100
                        }%`;
                    rangeSliderInputRight.value = scaledValue;
                    this.updateDisplayedValues(minValue, scaledValue);
                }
            }

            const currentMaxPosition = parseFloat(
                rangeSliderHandleRight.style.left
            );
            const currentMinPosition = parseFloat(
                rangeSliderHandleLeft.style.left
            );
            rangeSliderValRange.style.width = `${currentMaxPosition - currentMinPosition
                }%`;
        } else {
            const rangeSlider = this.shadow.querySelector("#RangeSlider");
            const rangeSliderValRange = rangeSlider.querySelector(
                ".range-slider-val-range"
            );
            const rangeSliderHandleLeft = rangeSlider.querySelector(
                ".range-slider-handle-left"
            );
            const rangeSliderInputLeft = this.shadow.querySelector(
                ".range-slider-input-left"
            );

            const rangeSliderRect = rangeSlider.getBoundingClientRect();

            let newValue =
                ((e.clientX - rangeSliderRect.left) / rangeSliderRect.width) *
                100;
            newValue = Math.max(0, Math.min(newValue, 100));

            const scaledValue = Math.round(
                this.min + (newValue / 100) * (this.max - this.min)
            );

            if (scaledValue <= this.max) {
                const leftPosition =
                    ((scaledValue - this.min) / (this.max - this.min)) * 100;

                rangeSliderHandleLeft.style.left = `${leftPosition}%`;
                rangeSliderValRange.style.width = `${leftPosition}%`;
                rangeSliderInputLeft.value = scaledValue;

                this.updateDisplayedValues(scaledValue);
            }
        }
    }

    stopDragging() {
        if (this.mouseMoveHandler) {
            // Utiliser la même référence pour supprimer l'écouteur
            document.removeEventListener("mousemove", this.mouseMoveHandler);
            this.mouseMoveHandler = null;
        }
        this.draggingHandle = null;
    }
    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //
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
        return this.getAttribute("color");
    }
    set color(value) {
        this.setAttribute("color", value);
    }
    set type(value) {
        this.setAttribute("type", value);
    }
    get type() {
        return this.getAttribute("type");
    }
}
