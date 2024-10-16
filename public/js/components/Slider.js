import { WebComponent } from './WebComponent.js';

export class Slider extends WebComponent {
    
    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
        this.renderComponent();
        this.addRangeEventListeners();
    }

    disconnectedCallback() {
        // Cleanup logic if needed
    }

    styles() {
        return `
        <style>
            .slider-container {
                display: flex;
                flex-direction: column;
                width: 300px;
                margin: 10px;
            }
            
            .labels {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 14px;
                margin-bottom: 5px;
            }
            
            .value-label {
                display: flex;
                justify-content: end;
                width: 80px;
                gap: 2px;
            }
            
            .slider {
                -webkit-appearance: none;
                appearance: none;
                width: 100%;
                height: 8px;
                background: #ddd;
                border-radius: 5px;
                outline: none;
            }

            .slider::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 15px;
                height: 15px;
                border-radius: 50%;
                background: ${this.color};
                cursor: pointer;
            }

            .range-slider {
                position: relative;
            }

            .range-slider input {
                pointer-events: none;
                position: absolute;
                width: 100%;
                height: 6px;
                
                -webkit-appearance: none;
                appearance: none;
                background: #ddd;
            }

            .range-slider input::-webkit-slider-thumb {
                pointer-events: all;
                position: relative;
                margin-top: -1px;
               
                background: #ffff;
                border: 2px solid ${this.color};

                border-radius: 100%;
                box-sizing: border-box;
                z-index: 2;
            }

            .range-slider .slider-track {
                position: absolute;
                height: 6px;
                background-color: ${this.color};
                border-radius: 5px;
                margin-top: 2px;
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
            <div class="range-slider">
            <input type="range" min="${this.min}" max="${this.max}" value="${this.min}" class="slider" id="minSlider">
            <input type="range" min="${this.min}" max="${this.max}" value="${this.max}" class="slider" id="maxSlider">
            <div class="slider-track"></div>
            </div>
        </div>
        `;
    }

    renderComponent() {
        this.shadow.innerHTML = this.styles() + this.render();
        this.updateTrack();
    }

    // Getters and setters for attributes

    get label() {
        return this.getAttribute('label') || 'Label';
    }

    set label(value) {
        this.setAttribute('label', value);
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

    get type() {
        return this.getAttribute('type') || 'value';
    }

    set type(value) {
        this.setAttribute('type', value);
    }

    // Lifecycle and interaction

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

    updateTrack() {
        if (this.type === 'minmax') {
            const minSlider = this.shadow.querySelector('#minSlider');
            const maxSlider = this.shadow.querySelector('#maxSlider');
            const track = this.shadow.querySelector('.slider-track');

            if (minSlider && maxSlider && track) {
                this.updateRangeTrack(minSlider, maxSlider, track);
            }
        }
    }

    addRangeEventListeners() {
        if (this.type === 'minmax') {
            const minSlider = this.shadow.querySelector('#minSlider');
            const maxSlider = this.shadow.querySelector('#maxSlider');
            const track = this.shadow.querySelector('.slider-track');
            
            if (minSlider && maxSlider && track) {
                minSlider.addEventListener('input', () => this.updateRangeTrack(minSlider, maxSlider, track));
                maxSlider.addEventListener('input', () => this.updateRangeTrack(minSlider, maxSlider, track));
            }
        }
    }

    updateRangeTrack(minSlider, maxSlider, track) {
        const minValue = parseInt(minSlider.value);
        const maxValue = parseInt(maxSlider.value);

        const trackWidth = ((maxValue - this.min) / (this.max - this.min)) * 100;
        const leftPosition = ((minValue - this.min) / (this.max - this.min)) * 100;

        track.style.left = `${leftPosition}%`;
        track.style.width = `${trackWidth - leftPosition}%`;
    }
}