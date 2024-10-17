import {WebComponent} from "./WebComponent.js";


/**
 * footer component
<<<<<<< HEAD
=======
 *
 * @arg {string} user - User connected to the website. Default is visitor
>>>>>>> 3175943714acfda805b1415c163381eb7a40e285
 */
export class Footer extends WebComponent {

    static get observedAttributes() {
        return []
    }

    constructor() {
        super();
<<<<<<< HEAD
=======

        this.resolveUser();
>>>>>>> 3175943714acfda805b1415c163381eb7a40e285
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
<<<<<<< HEAD
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
                }
                
                p {
                    margin: 0;
                    color: rgb(var(--color-gray-4));
                    font-size: var(--typescale-base);
                    font-family: var(--font-text);
                }
                
                #parts {
                    width: 100%;
                    display: flex;
                    justify-content: space-between;
                }
            
                #parts p {
                    margin-bottom: 5px;
                    font-size: var(--typescale-u1);
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
                    margin-right: 10px;
                }
            
                #networks {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                    order: 1;
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
=======
               
>>>>>>> 3175943714acfda805b1415c163381eb7a40e285
            </style>
        `;
    }

    render() {
        return `
<<<<<<< HEAD
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
                                <a href="">Conditions d'utilisation</a>
                                <a href="">Confidentialité et utilisation des cookies</a>
                                <a href="">Plan du site</a>
                                <a href="">Contactez-nous</a>
                            </div>
                        </div>
                    </div>
                    <div id="networks">
                        <a href=""><img src="images/insta.png" alt="Instagram"></a>
                        <a href=""><img src="images/facebook.png" alt="Facebook"></a>
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


=======
            
        `;
    }

>>>>>>> 3175943714acfda805b1415c163381eb7a40e285
    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //

<<<<<<< HEAD
=======
    resolveUser() {
        switch (this.user) {
            case "pro":
                this.addStyleVariable("logo", "");
                this.addStyleVariable("color", "rgb(var(--color-purple-primary))");
                this.addStyleVariable("border", "none");

                this.addStyleVariable("color", "rgb(var(--color-purple-primary))");
                this.addStyleVariable("border", "1px var(--color-purple-primary)) solid inside bottom");
                break;
            default:
                this.addStyleVariable("logo", "");
                this.addStyleVariable("color", "rgb(var(--color-blue-primary))");
                this.addStyleVariable("border", "none");

                this.addStyleVariable("color", "rgb(var(--color-blue-primary))");
                this.addStyleVariable("border", "1px var(--color-blue-primary)) solid inside bottom");
        }
    }
>>>>>>> 3175943714acfda805b1415c163381eb7a40e285

    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

<<<<<<< HEAD
=======
    /**
     * @returns {"pro"|"visitor"}
     */
    get user() {

    }

    /**
     * @returns {boolean}
     */
    get connected() {

    }
>>>>>>> 3175943714acfda805b1415c163381eb7a40e285
}