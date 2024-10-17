import { WebComponent } from '../WebComponent.js';

export class Slider extends WebComponent {
    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
        this.renderComponent();
        this.addRangeEventListeners();
    }

    styles() {
        return `
        <style>
            .slider-container {
                position: relative;
                width: 300px;
                margin: 20px;
            }
            .labels {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 14px;
                margin-bottom: 5px;
            }
            .slider {
                -webkit-appearance: none;
                width: 100%;
                height: 8px;
                background: transparent;
                position: absolute;
                top: 10px;
                z-index: 3; /* To ensure it is on top */
                cursor: pointer;
            }

            .slider::-webkit-slider-thumb {
                -webkit-appearance: none;
                width: 15px;
                height: 15px;
                border-radius: 50%;
                background: ${this.color};
                cursor: pointer;
            }

            .slider::-moz-range-thumb {
                width: 15px;
                height: 15px;
                border-radius: 50%;
                background: ${this.color};
                cursor: pointer;
            }

            .range {
                position: absolute;
                height: 8px;
                background-color: ${this.color};
                border-radius: 5px;
                top: 10px;
                z-index: 2;
            }

            .thumb {
                position: absolute;
                top: 0;
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background-color: white;
                border: 2px solid ${this.color};
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            }

            .value-label {
                display: flex;
                justify-content: space-between;
                margin-top: 30px;
            }
        </style>
        `;
    }

    render() {
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
            <input type="range" min="${this.min}" max="${this.max}" value="${this.min}" class="slider" id="minSlider">
            <input type="range" min="${this.min}" max="${this.max}" value="${this.max}" class="slider" id="maxSlider">
            <div class="range" id="range"></div>
            <div class="thumb" id="minThumb" style="left: ${this.getPercentage(this.min)}%;"></div>
            <div class="thumb" id="maxThumb" style="left: ${this.getPercentage(this.max)}%;"></div>
        </div>
        `;
    }

    renderComponent() {
        this.shadow.innerHTML = this.styles() + this.render();
        this.updateTrack(); // Ensure the track is updated on initial render
    }

    getPercentage(value) {
        return ((value - this.min) / (this.max - this.min)) * 100;
    }

    updateTrack() {
        const minSlider = this.shadow.querySelector('#minSlider');
        const maxSlider = this.shadow.querySelector('#maxSlider');
        const range = this.shadow.querySelector('#range');
        const minThumb = this.shadow.querySelector('#minThumb');
        const maxThumb = this.shadow.querySelector('#maxThumb');

        const minValue = parseInt(minSlider.value);
        const maxValue = parseInt(maxSlider.value);

        const minPercent = this.getPercentage(minValue);
        const maxPercent = this.getPercentage(maxValue);

        range.style.left = `${minPercent}%`;
        range.style.width = `${maxPercent - minPercent}%`;

        minThumb.style.left = `${minPercent}%`;
        maxThumb.style.left = `${maxPercent}%`;

        this.shadow.querySelector('#minValue').textContent = minValue;
        this.shadow.querySelector('#maxValue').textContent = maxValue;
    }

    addRangeEventListeners() {
        const minSlider = this.shadow.querySelector('#minSlider');
        const maxSlider = this.shadow.querySelector('#maxSlider');

        minSlider.addEventListener('input', () => {
            // Ensure the min slider cannot go above the max slider
            if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
                minSlider.value = maxSlider.value; // Set min to max if they overlap
            }
            this.updateTrack();
        });

        maxSlider.addEventListener('input', () => {
            // Ensure the max slider cannot go below the min slider
            if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
                maxSlider.value = minSlider.value; // Set max to min if they overlap
            }
            this.updateTrack();
        });
    }

    // Getters and setters for attributes
    get label() {   
        return this.getAttribute('label') || 'Label';
    }

    get min() {
        return parseInt(this.getAttribute('min')) || 0;
    }

    set min(value) {
        this.setAttribute('min', value);
    }

    get max() {
        return parseInt(this.getAttribute('max')) || 100;
    }

    set max(value) {
        this.setAttribute('max', value);
    }

    get color() {
        return this.getAttribute('color') || 'blue';
    }

    set color(value) {
        this.setAttribute('color', value);
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this[name] = newValue;
            this.reRender();
        }
    }

    reRender() {
        this.renderComponent();
        this.addRangeEventListeners();
    }
}
