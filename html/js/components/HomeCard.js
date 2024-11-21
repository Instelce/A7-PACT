import { WebComponent } from './WebComponent.js';

/**
 * SearchPageCard component
 *
 * @arg {string} image - Link to the image
 * @arg {string} title - Activity's title
 * @arg {string} author - Activity's author
 * @arg {string} type - Activity's type
 * @arg {string} price - Activity's price
 * @arg {string} location - Activity's location
 * @arg {string} description - Travel time between location and user
 * @arg {string} daysSinceOnline - Date of publication
 *
 * @extends {WebComponent}
 */
export class HomeCard extends WebComponent {

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
    }

    styles() {
        return `
            <style>
                .enLigne {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: nowrap;
                    align-items: center;
                    gap: 5px;
                }
                
                .enLigneGap{
                    display: flex;
                    flex-direction: row;
                    flex-wrap: nowrap;
                    align-items: center;
                    gap: 20px;
                }
                
                .opposeLigne {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: nowrap;
                    justify-content: space-between;
                }
                
                .card {
                    display: flex;
                    flex-direction: column;
                    flex-wrap: nowrap;
                    background-color: white;
                    border: 1px solid #E1E1E1;
                    border-radius: 12px;
                    width: 301px;
                    height: 426px;
                }
                
                .cardContent{
                    padding: 0 20px 5px 10px;
                }
                
                div > slot::slotted(img) {
                    width: 100%;
                    height: 180px;
                    object-fit: cover;
                    border-top-left-radius: 12px;
                    border-top-right-radius: 12px;
                }

                
                p{
                    font-family: var(--font-text);
                    font-size: 0.8em;
                    margin: 7px 0;
                    color: var(--color-gray-4);
                }
                
                h1{
                    font-family: var(--font-title);
                    font-size: 1em;
                    font-weight: 600;
                    margin: 20px 0 10px 0;
                }
                
                .name{
                    text-decoration: underline;
                }
                
                
            </style>
        `;
    }

    render() {
        return `
    <div class="card">
        <slot class="image" name="image"></slot>

        <div class="cardContent">
            <h1><slot name="title"></slot></h1>
            <div class="enLigne">
                <p class="par">par</p>
                <p class="name"><slot name="author"></slot></p>
            </div>

            <div class="enLigneGap">
                <p><slot name="type"></slot></p>
                <div class="enLigne">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <p><slot name="location"></slot></p>
                </div>
            </div>

            <div class="description">
                <p><slot name="description"></slot></p>
            </div>


            <div class="enLigneGap">
                <!-- <div class="enLigne">
                    <p>images etoiles</p>       
                    <p>4.5/5</p>
                </div> -->
                <div class="enLigne">
                    <p>232</p>
                    <p>Avis</p>
                </div>
            </div>


            <div class="opposeLigne">
                <div class="enLigne">
                    <p>Il y a</p>
                    <p><slot name="daysSinceOnline"></slot></p>
                    <p>j</p>
                </div>
                
                <div class="enLigne">
                    <p>Dès </p>
                    <p><slot name="price"></slot></p>
                    <p>€ / Personne</p>
                </div>
            </div>

        </div>
    </div>
        `;
    }
}
