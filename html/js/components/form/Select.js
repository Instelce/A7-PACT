import { WebComponent } from "../WebComponent.js";

export class Select extends WebComponent {
    constructor() {
        super();

        this.input = this.querySelector("input");
        this.input.id = this.id;
        this.input.setAttribute("name", this.getAttribute("name"));
        this.input.setAttribute("value", this.getAttribute("value") ?? "");
        if (this.hasAttribute("required"))
            this.input.setAttribute("required", "");

        this.input.addEventListener("change", (event) => {
            const customEvent = new Event("change", {
                bubbles: true,
                composed: true,
            });
            this.dispatchEvent(customEvent);
        });

        this.trigger = this.shadow.querySelector(".trigger");
        this.chevron = this.shadow.querySelector(".chevron");
        this.optionsContainer = this.querySelector('[slot="options"]');
        this.optionsButton = this.querySelectorAll('[slot="options"] > *');
        this.triggerValue = this.querySelector('[slot="trigger"]');

        // Open the options when the trigger is clicked
        this.trigger.addEventListener("click", () => {
            this.optionsContainer.classList.toggle("open");
            this.chevron.classList.toggle("rotate");
        });

        // Set the role of the options container
        this.optionsContainer.setAttribute("role", "listbox");
        this.trigger.setAttribute("aria-haspopup", "listbox");

        this.optionsButton.forEach((option) => {
            // Set role
            option.setAttribute("role", "option");
            option.setAttribute("focusable", "true");

            // Set the selected option at the beginning
            if (option.getAttribute("data-value") === this.input.value) {
                this.triggerValue.innerHTML = option.textContent;
                option.classList.add("selected");
            }
        });

        // Add the check icon to the options and option class
        this.optionsButton.forEach((option) => {
            option.classList.add("option");

            let check = document.createElement("div");
            check.classList.add("check");
            if (option.getAttribute("data-value") !== this.input.value) {
                check.classList.add("opacity-0");
            }
            check.innerHTML = this.renderCheck();
            option.appendChild(check);
        });

        this.optionsButton.forEach((option) => {
            option.addEventListener("click", () => {
                if (!option.classList.contains("selected")) {
                    this.input.value = option.getAttribute("data-value");
                    this.triggerValue.innerHTML = option.textContent;
                    this.querySelector('[slot="options"]').classList.remove(
                        "open"
                    );
                    this.chevron.classList.remove("rotate");

                    this.optionsButton.forEach((option) => {
                        let check = option.querySelector(".check");
                        check.classList.add("opacity-0");
                        option.classList.remove("selected");
                    });

                    let check = option.querySelector(".check");
                    check.classList.remove("opacity-0");
                    option.classList.add("selected");

                    this.input.dispatchEvent(new Event("change"));
                }
            });
        });

        // Click outside the select to close the options
        document.addEventListener("click", (event) => {
            if (!this.contains(event.target)) {
                this.optionsContainer.classList.remove("open");
                this.chevron.classList.remove("rotate");
            }
        });

        // Keyboard navigation
        this.trigger.addEventListener("keydown", (event) => {
            if (event.key === "ArrowDown") {
                this.optionsContainer.classList.add("open");
                this.chevron.classList.add("rotate");
                this.optionsButton[0].focus();
            }

            if (event.key === "Escape") {
                this.optionsContainer.classList.remove("open");
                this.chevron.classList.remove("rotate");
            }
        });

        // Add required asterisk
        if (this.hasAttribute("required")) {
            let label = this.querySelector('[slot="label"]');
            label.innerHTML += `
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgb(var(--color-gray-4))" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-asterisk"><path d="M12 6v12"/><path d="M17.196 9 6.804 15"/><path d="m6.804 9 10.392 6"/></svg>
            `;
        }
    }

    connectedCallback() {
        super.connectedCallback();
    }

    styles() {
        return `
        <style>
            .select {
                width: 100%;
                min-width: 200px;
                position: relative;
            }
            
            .trigger {
                width: 100%;
                padding: .8rem 1.5rem;
                
                border: 1px solid rgb(var(--color-gray-2));
                border-radius: var(--radius-small);
                background: none;
                
                font-size: inherit;
                
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                
                cursor: pointer;
            }
            
            ::slotted([slot="label"]) {
                display: block;
                margin-bottom: .5rem;
            }
            
            .chevron {
                transition: transform .1s;
            }
            
            .chevron.rotate {
                transform: rotate(180deg);
            }
            
            ::slotted([slot="label"]) {
                display: flex;
                align-items: center;
                gap: .5rem;
            }
        </style>
        `;
    }

    render() {
        return `
            <slot name="label"></slot>
            <div class="select">
                <button class="trigger">
                    <slot name="trigger"></slot>
                    ${this.renderChevron()}
                </button>
                
                <slot name="options"></slot>
            </div>
        `;
    }

    noScope() {
        return `
            <input type="hidden">
        `;
    }

    renderChevron() {
        return `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down chevron"><path d="m6 9 6 6 6-6"/></svg>
        `;
    }

    renderCheck() {
        return `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>
        `;
    }
}
