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
        width: 100%;
        height: 300px;
        overflow: hidden; 
    }

    .slider-container {
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
        margin-right: 10px;
        width: 50px;
        height: 50px;
        
        display: flex;
        justify-content: center;
        align-items: center;

        pointer-events: auto;
    }
    
   .button {
    --background: var(--color-blue-primary);
    --border: var(--color-blue-primary);
    --color: var(--color-white);
    --background-hover: var(--color-white);
    --border-hover: var(--color-blue-primary);
    --color-hover: var(--color-blue-primary);

    padding: .8rem 2rem;

    background: rgb(var(--background));
    border: 1px solid rgb(var(--border));
    border-radius: var(--radius-rounded);
    outline: none;

    font-size: inherit;
    font-weight: 500;
    white-space: nowrap;
    color: rgb(var(--color));

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;

    cursor: pointer;
    transition: color .2s, background .2s, border .2s, box-shadow .2s;
}

    .button:hover {
        background: rgb(var(--background-hover));
        border: 1px solid rgb(var(--border-hover));
        color: rgb(var(--color-hover));
    }
    
    .button.only-icon {
        padding: 0;
    }
    
    .button.only-icon.sm {
        height: 2.5rem;
    }
    
    .button.only-icon {
        width: 3rem;
        height: 3rem;
    }
    
    .button.only-icon.lg {
        width: 3.5rem;
        height: 3.5rem;
    }
    
    .button:focus {
        box-shadow: 0 0 0 3px rgba(var(--background), .5);
        transform: scale(1.02);
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
        <button id="prev" class="button only-icon">&lt;</button>        
        <button id="next" class="button only-icon">&gt;</button>  
    </div>
</div>
        `;
    }
}
