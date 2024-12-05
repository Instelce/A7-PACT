import {WebComponent} from "../WebComponent.js";

const KEYCODE = {
    DOWN: 40,
    LEFT: 37,
    RIGHT: 39,
    UP: 38,
    HOME: 36,
    END: 35,
};

/**
 * Tabs component
 */
export class Tabs extends WebComponent {

    static get observedAttributes() {
        return []
    }

    constructor() {
        super();

        this.onSlotChange = this.onSlotChange.bind(this);

        this.tabSlot = this.shadow.querySelector('slot[name="tab"]');
        this.panelSlot = this.shadow.querySelector('slot[name="panel"]');

        // Add on slot change event listener
        this.tabSlot.addEventListener('slotchange', this.onSlotChange);
        this.panelSlot.addEventListener('slotchange', this.onSlotChange);

        this.saveInGet = this.hasAttribute('save');
    }

    connectedCallback() {
        super.connectedCallback();

        this.addEventListener('keydown', this.onKeyDown);
        this.addEventListener('click', this.onClick);

        if (!this.hasAttribute('role')) {
            this.setAttribute('role', 'tablist');
        }

        Promise.all([
            customElements.whenDefined('howto-tab'),
            customElements.whenDefined('howto-panel'),
        ]).then(_ => this.linkPanels());
    }

    disconnectedCallback() {
        this.removeEventListener('keydown', this.onKeyDown);
        this.removeEventListener('click', this.onClick);
    }

    attributeChangedCallback(name, oldValue, newValue) {
    }

    styles() {
        return `
        <style>
          .triggers {
              width: 100%;
              display: flex;
          }
          
          :host(.column) .triggers {
              width: auto;
              flex-direction: column;
          }
          
          @media screen and (max-width: 768px){
              :host(.column) .triggers {
                  display: none;
              }
          }
        </style>
        `;
    }

    render() {
        return `
        <div class="triggers">
            <slot name="tab"></slot>
        </div>
        <slot name="panel"></slot>
        `;
    }


    // ---------------------------------------------------------------------- //
    // Other functions
    // ---------------------------------------------------------------------- //

    onSlotChange() {
        this.linkPanels();
    }

    linkPanels() {
        const tabs = this.allTabs();

        tabs.forEach(tab => {
            const panel = tab.nextElementSibling;
            if (panel.tagName.toLowerCase() !== 'x-tab-panel') {
                console.error(`Tab #${tab.id} is not a sibling of a <x-tab-panel>`);
                return;
            }

            tab.setAttribute('aria-controls', panel.id);
            panel.setAttribute('aria-labelledby', tab.id);
        })

        let selectedTab = tabs.find(tab => tab.selected) || tabs[0];

        if (this.saveInGet) {
            const urlParams = new URLSearchParams(window.location.search);
            const tabId = urlParams.get('tab');
            if (tabId) {
                selectedTab = tabs.find(tab => tab.id === tabId) || selectedTab;
            }
        }

        this.selectTab(selectedTab);
    }

    allPanels() {
        return Array.from(this.querySelectorAll('x-tab-panel'));
    }

    allTabs() {
        return Array.from(this.querySelectorAll('x-tab'));
    }

    panelForTab(tab) {
        const panelId = tab.getAttribute('aria-controls');
        return this.querySelector(`#${panelId}`);
    }

    prevTab() {
        const tabs = this.allTabs();

        let newIdx = tabs.findIndex(tab => tab.selected) - 1;

        return tabs[(newIdx + tabs.length) % tabs.length];
    }

    firstTab() {
        return this.allTabs()[0];
    }

    lastTab() {
        const tabs = this.allTabs();
        return tabs[tabs.length - 1];
    }

    nextTab() {
        const tabs = this.allTabs();

        let newIdx = tabs.findIndex(tab => tab.selected) + 1;

        return tabs[newIdx % tabs.length];
    }

    reset() {
        this.allTabs().forEach(tab => tab.selected = false);
        this.allPanels().forEach(panel => panel.hidden = true);
    }

    selectTab(tab) {
        this.reset();

        const panel = this.panelForTab(tab);

        if (!panel) {
            throw new Error('No panel with id ' + tab.getAttribute('aria-controls'));
        }

        tab.selected = true;
        panel.hidden = false;
        tab.focus();
    }

    onKeyDown(event) {
        if (event.target.getAttribute('role') !== 'tab') {
            return;
        }

        if (event.altKey) {
            return;
        }

        let newTab;
        switch (event.keyCode) {
            case KEYCODE.LEFT:
            case KEYCODE.UP:
                newTab = this.prevTab();
                break;
            case KEYCODE.RIGHT:
            case KEYCODE.DOWN:
                newTab = this.nextTab();
                break;
            case KEYCODE.HOME:
                newTab = this.firstTab();
                break;
            case KEYCODE.END:
                newTab = this.lastTab();
                break;
            default:
                return;
        }

        event.preventDefault();

        this.selectTab(newTab);
    }

    onClick(event) {
        if (event.target.getAttribute('role') !== 'tab') {
            return;
        }

        this.selectTab(event.target);

        if (this.saveInGet) {
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('tab', event.target.id);
            history.replaceState(null, '', newUrl);
        }
    }
}