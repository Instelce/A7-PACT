import {WebComponent} from "./WebComponent.js";


/**
 * footer component
 *
 */
export class Footer extends WebComponent {

    static get observedAttributes() {
        return []
    }

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();
    }

    disconnectedCallback() {
    }

    attributeChangedCallback(name, oldValue, newValue) {
    }

    styles() {
        return `
            <style>
                
                footer {
                    max-width: 840px;
                    width: 100%;
                    height: 300px;
                    display: flex;
                    flex-direction: column;
                    background-color: rgb(var(--color-footer),0.2);
                    padding: 25px 300px;
                    justify-content: flex-start;
                    align-items: center;
                    gap: 10px;
                    font-family: var(--font-text);
                    font-size: var(--typescale-d1);
                    color: rgb(var(--color-gray-4));
                }
                
                p {
                    margin: 0;           
                }
                
                #parts {
                    width: 100%;
                    display: flex;
                    justify-content: space-between;
                }
            
                #parts p {
                    margin-bottom: 5px;
                    font-size: var(--typescale-base);
                }
            
                #parts a {
                    text-decoration: none;
                    color : rgb(var(--color-black))
                }
            
                #about, #explore, #solutions {
                    display: flex;
                    flex-direction: column;
                }
            
                nav {
                    width: 100%;
                    display: flex;
                    justify-content: space-between;
                }
            
                #conditions {
                    display: flex;
                    gap: 20px;
                    align-items: center;
                    width: 70%;
                    order: 0;
                }
            
                #logo {
                    width: 85px;
                    height: auto;
                    object-fit: contain;
                }
            
                #links {
                    display: flex;
                    flex-wrap: wrap;
                }
            
                #links a {
                    text-decoration: none;
                    margin-right: 10px;
                }
            
                #networks {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                    order: 1;
                }
                
                #networks a{
                    padding: 10px;
                    border: solid 1px rgb(var(--color-gray-2));
                    border-radius: 100%;
                    color: rgb(var(--color-black));
                }
            
                #trip {
                    display: flex;
                    gap: 20px;
                    align-items: center;
                    width: 100%;
                }
            
                #trip img {
                    object-fit: contain;
                    width: 30px;
                }
            
                #finance {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    flex-wrap: nowrap;
                    width: 100%;
                    margin-left: 206px;
                }
            
                #finance a {
                    color: rgb(var(--color-black));
                }
                
                .blueLink {
                    color: rgb(var(--color-blue-primary));
                    position: relative;
                }
                
                .blueLink::after {
                    content: '';
                    position: absolute;
                    width: 100%;
                    height: 1px;
                    bottom: -2px;
                    left: 0;
                    background-color: rgb(var(--color-blue-primary));
                    transform: scaleX(0);
                    transform-origin: bottom right;
                    transition: transform 0.25s ease-out;
                }
                
                .blueLink:hover::after {
                    transform: scaleX(1);
                    transform-origin: bottom left;
                }
                
                .purpleLink {
                    color: rgb(var(--color-purple-primary));
                    position: relative;
                }
                
                .purpleLink::after {
                    content: '';
                    position: absolute;
                    width: 100%;
                    height: 1px;
                    bottom: -2px;
                    left: 0;
                    background-color: rgb(var(--color-purple-primary));
                    transform: scaleX(0);
                    transform-origin: bottom right;
                    transition: transform 0.25s ease-out;
                }
                
                .purpleLink:hover::after {
                    transform: scaleX(1);
                    transform-origin: bottom left;
                }
                
            
                @media (max-width: 768px) {
                    footer {
                        width: 100%;
                        height: auto;
                        padding: 20px;
                        justify-content: center;
                        gap: 20px;
                    }
            
                    #parts {
                        flex-direction: column;
                        gap: 20px;
                    }
            
                    nav {
                        flex-direction: column;
                        gap: 20px;
                    }
            
                    #conditions {
                        width: 100%;
                        order: 1;
                    }
            
                    #networks {
                        justify-content: flex-start;
                        align-items: center;
                        order: 0;
                    }
                    
            
                    #trip, #finance {
                        width: 100%;
                        margin-left: 0;
                        flex-wrap: wrap;
                    }
                }
            </style>
        `;
    }

    render() {
        return `
            <footer>
                <div id="parts">
                    <div id="about">
                        <p>À propos</p>
                        <a href="">À propos de PACT</a>
                        <a href="">Règlements</a>
                    </div>
                    <div id="explore">
                        <p>Explorez</p>
                        <a href="">Écrire un avis</a>
                        <a href="">S'inscrire</a>
                    </div>
                    <div id="solutions">
                        <p>Utilisez nos solutions</p>
                        <a href="">Professionnel</a>
                    </div>
                </div>
                <nav>
                    <div id="conditions">
                        <img id="logo" src="images/logo (2).png" alt="Logo PACT">
                        <div>
                            <p>@ 2024 PACT Tous droits réservés.</p>
                            <div id="links">
                                <a class="blueLink" href="">Conditions d'utilisation</a>
                                <a class="blueLink" href="">Confidentialité et utilisation des cookies</a>
                                <a class="blueLink" href="">Plan du site</a>
                                <a class="blueLink" href="">Contactez-nous</a>
                            </div>
                        </div>
                    </div>
                    <div id="networks">
                        <a href="https://www.instagram.com/"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg></a>
                        <a href="https://www.facebook.com/"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                    </div>
                </nav>
                <div id="trip">
                    <img id="tripenarvor" src="images/TripEnArvorLogo.png" alt="Logo TripEnArvor">
                    <p>Plateforme proposée par <b>TripEnArvor</b></p>
                </div>
                <div id="finance">
                    <p>Projet financé par la</p>
                    <a href="">Région Bretagne</a>
                    <p>et par le</p>
                    <a href="">Conseil Général des Côtes d’Armor.</a>
                </div>
            </footer>
        `;
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //


    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

}