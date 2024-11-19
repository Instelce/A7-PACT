import { WebComponent } from './WebComponent.js';

/**
 * SearchPageCard component
 *
 * @arg {string} image - Link to the image
 * @arg {string} title - Activity's title
 * @arg {string} author - Activity's author
 * @arg {string} type - Activity's type
 * @arg {string} info - Activity's info
 * @arg {string} location - Activity's location
 * @arg {string} locationDistance - Travel time between location and user
 * @arg {string} date - Date of publication
 *
 * @extends {WebComponent}
 */
export class SearchPageCard extends WebComponent {

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
    }

    styles() {
        return `
            <style>
                .searchPageCard {
                    display: flex;
                    align-items: center;
                    max-width: 600px;
                }
                
                .searchPageCard ::slotted(img) {
                    object-fit: cover;
                    width: 100px;
                    height: 100px !important;
                    border-radius: 20px;
                    margin-right: 10px;
                }
                .content {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    height: 80px;
                }
        
                .location {
                    display: flex;
                    align-items: center;
                }
                .location span {
                    margin-right: 5px;
                }
        
                p{
                    font-family: var(--font-text);
                    font-size: 0.8em;
                    margin: 0;
                    color: var(--color-gray-4);
                }
        
                svg{
                    margin-left: -2px;
                }
        
                h3{
                    font-family: var(--font-text);
                    font-size: 1em;
                    font-weight: 600;
                    margin: 0;
                }
        
                .name{
                    text-decoration: underline;
                }
        
                .name,.par {
                    display: none;
                }
        
                .displayName{
                    display: flex;
                    flex-direction: row;
                    flex-wrap: wrap;
                    align-items: center;
                    gap: 5px;
                }
        
        
                @media only screen and (min-width: 768px){
                    .name,.par {
                        display: block;
                    }
                }
    
            </style>
        `;
    }

    render() {
        return `
            <div class="searchPageCard">
                <slot name="image"></slot>
                
                <div class="content">
                    <div class="displayName">
                        <h3><slot name="title"></slot></h3>
                        <p class="par">par</p>
                        <p class="name"><slot name="author"></slot></p>
                    </div>

                    <p><slot name="type"></slot><slot name="info"></slot></p>

                    <div class="location">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </span>
                        <p><slot name="location"></slot><slot name="locationDistance"></slot><slot name="date">Date</slot></p>
                    </div>
                </div>
            </div>
        `;
    }
}
