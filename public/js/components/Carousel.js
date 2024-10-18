import { WebComponent } from './WebComponent.js';

/**
 * Carousel component
 *
 * @extends {WebComponent}
 */
export class Carousel extends WebComponent {
    constructor() {
        super();
        this.images = [];
    }

    connectedCallback() {
        super.connectedCallback();
        this.renderSlides();

        const slider = this.shadowRoot.getElementById('slider');
        const prevBtn = this.shadowRoot.getElementById('prev');
        const nextBtn = this.shadowRoot.getElementById('next');

        let currentIndex = 0;

        const updateCurrentIndex = () => {
            const slideWidth = slider.querySelector('.slide').offsetWidth + 10;
            currentIndex = Math.round(slider.scrollLeft / slideWidth);
        };

        const scrollToSlide = (index) => {
            const slideWidth = slider.querySelector('.slide').offsetWidth + 10;
            slider.scrollTo({
                left: index * slideWidth,
                behavior: 'smooth'
            });
        };

        nextBtn.addEventListener('click', () => {
            if (currentIndex < slider.children.length - 1) {
                currentIndex++;
                scrollToSlide(currentIndex);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                scrollToSlide(currentIndex);
            }
        });

        slider.addEventListener('scroll', updateCurrentIndex);
        window.addEventListener('resize', () => {
            scrollToSlide(currentIndex);
        });
    }

    renderSlides() {
        const slotImages = this.querySelectorAll('img[slot="image"]');
        const slidesContainer = this.shadowRoot.getElementById('slider');

        // Créez des divs pour chaque image et les ajoutez au conteneur
        slotImages.forEach((img) => {
            const slide = document.createElement('div');
            slide.classList.add('slide');
            slide.appendChild(img.cloneNode(true)); // Clonez l'image pour éviter les problèmes de référence
            slidesContainer.appendChild(slide);
        });
    }

    styles() {
        return `
<style>
    .slider-wrapper {
        display: flex;
        flex-direction: row;
        align-items: center;
        position: relative;
        width: 100vw;
        height: 300px;
        overflow: hidden; 
    }

    .slider-container {
        margin: 10px;
        display: flex;
        height: 100%;
        overflow-x: scroll;
        scroll-snap-type: x mandatory;
        scrollbar-width: none; 
    }

    .slider-container::-webkit-scrollbar {
        display: none; 
    }

    .slide {
        flex-shrink: 0;
        scroll-snap-align: start;
        margin-right: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .slide img {
        width: 100%; 
        max-width: 350px; 
        height: 250px;
        object-fit: cover;
        border-radius: 20px;
    }

    .controls {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
        pointer-events: none;
    }

    button {
        margin-left: 10px;
        margin-right: 15px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #007BFF;
        color: white;
        border: none;
        font-size: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        pointer-events: auto;
    }

    button:hover {
        background-color: #f1f1f1;
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
    }
</style>
        `;
    }

    render() {
        return `
<div class="slider-wrapper">
    <div class="slider-container" id="slider">
    </div>

    <div class="controls">
        <button id="prev">&lt;</button>
        <button id="next">&gt;</button>
    </div>
</div>
        `;
    }
}
