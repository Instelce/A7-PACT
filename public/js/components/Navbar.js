import { WebComponent } from "./WebComponent.js";

export class Navbar extends WebComponent {

    static get observedAttributes() { return [] }

    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();

        let navIcon = document.getElementById('nav-icon3');
        let menu = document.getElementById('menu');
        let closeMenu = document.getElementById('close-menu');

        // navIcon.addEventListener('click', function() {
        //     navIcon.classList.toggle('open');
        //     menu.classList.toggle('menu-hidden');
        //     menu.classList.toggle('menu-visible');
        // });
        //
        // closeMenu.addEventListener('click', function() {
        //     menu.classList.remove('menu-visible');
        //     menu.classList.add('menu-hidden');
        //     navIcon.classList.remove('open');
        // });
    }

    disconnectedCallback() {
        super.disconnectedCallback();
    }

    attributeChangedCallback(name, oldValue, newValue) {}

    styles() {
        return `
            <style>
            
                body{
                    margin: 0;
                    padding: 0;
                }
            
                #logo {
                    padding-top: 5px;
                    height: 64px;
                }
            
                .navbar {
                    display: flex;
                    flex-direction: row;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0 25px;
                    z-index: 1001;
                    position: relative;
                    background-color: white;
                    top: 0;
                }
            
                #nav-icon3 {
                    width: 35px;
                    height: 30px;
                    position: relative;
                    margin: 0;
                    transform: rotate(0deg);
                    transition: .5s ease-in-out;
                    cursor: pointer;
                    z-index: 1002;
                }
            
                #nav-icon3 span {
                    display: block;
                    position: absolute;
                    height: 2px;
                    width: 100%;
                    background: black;
                    border-radius: 3px;
                    opacity: 1;
                    left: 0;
                    transform: rotate(0deg);
                    transition: .25s ease-in-out;
                }
            
                #nav-icon3 span:nth-child(1) {
                    top: 0px;
                }
            
                #nav-icon3 span:nth-child(2),
                #nav-icon3 span:nth-child(3) {
                    top: 13px;
                }
            
                #nav-icon3 span:nth-child(4) {
                    top: 26px;
                }
            
                #nav-icon3.open span:nth-child(1) {
                    top: 13px;
                    width: 0%;
                    left: 50%;
                }
            
                #nav-icon3.open span:nth-child(2) {
                    transform: rotate(45deg);
                }
            
                #nav-icon3.open span:nth-child(3) {
                    transform: rotate(-45deg);
                }
            
                #nav-icon3.open span:nth-child(4) {
                    top: 13px;
                    width: 0%;
                    left: 50%;
                }
            
                #menu {
                    position: fixed;
                    top: 0;
                    left: -100%; 
                    width: 100vw;
                    height: 100vh;
                    background-color: white;
                    z-index: 1000;
                    transition: left 0.5s ease; 
                    padding: 50px;
                    box-sizing: border-box;
                }
            
                #menu.menu-visible {
                    left: 0; 
                }
            
                #menu ul {
                    list-style-type: none;
                    padding: 0;
                    margin: 0;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    height: 100%;
                }
            
                #menu ul li {
                    margin: 20px 0;
                }
            
                #menu ul li a {
                    text-decoration: none;
                    color: black;
                    font-size: 24px;
                    text-align: center;
                    display: block;
                }
            
                .menu-hidden {
                    left: -100%; 
                }
            
                .close-menu {
                    font-size: 40px;
                    color: black;
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    cursor: pointer;
                }
            
                x-button{
                    display : none;
                }
            
                .row{
                    display: flex;
                    justify-content: space-around;
                    align-items: baseline;
                    gap: 30px;
                }
            
                @media only screen and (min-width: 768px){
                    .navbar{
                        padding: 0 20%;
                    }
                }
            
                @media only screen and (min-width: 1440px){
            
                    .navbar{
                        padding: 0 20px;
                    }
            
                    menu{
                        display: none;
                    }
            
                    #nav-icon3{
                        display: none;
                    }
            
                    x-button{
                        display: block;
                    }
                }
            </style>
        `;
    }

    render() {
        return `
        <nav class="navbar">
            <div id="nav-icon3">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div>
                <img id="logo" src="../../assets/images/logoBlue.png" alt="">
            </div>
            <div class="row">
                <x-button>Connexion</x-button>
            </div>
        </nav>
    
        <div id="menu" class="menu-hidden">
            <ul>
                <li><a href="#link1">Lien 1</a></li>
                <li><a href="#link2">Lien 2</a></li>
                <li><a href="#link3">Lien 3</a></li>
                <li><a href="#link4">Lien 4</a></li>
            </ul>
        </div>
        
        `;
    }

    // ---------------------------------------------------------------------- //
    // Other methods
    // ---------------------------------------------------------------------- //


    // ---------------------------------------------------------------------- //
    // Getter and setter
    // ---------------------------------------------------------------------- //

}
