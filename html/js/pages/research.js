//initialize the global parameters and save them permanently
let filters = {};
let order = null;
let limit = 5;
let offset = 0;
/**
 * Displays the fetched offers data in the offers container.
 *
 * @function displayOffers
 * @param {Array} Data - An array of offer objects to be displayed.
 * @throws {Error} If the offers container is not found or if the data is not an array.
 */
// ---------------------------------------------------------------------------------------------- //
// Fetch data
// ---------------------------------------------------------------------------------------------- //

/**
 * Fetches offers data from the API based on provided filters, limit, offset, and order.
 *
 * @async
 * @function getOffers
 * @param {Object} [filters={}] - An object containing various filters for the offers.
 * @param {string} [filters.category] - The category of the offers.
 * @param {number} [filters.rating] - The minimum number of opinions for the offers.
 * @param {number} [filters.q] - The search data.
 * @param {number} [filters.minimumPrice] - The minimum price of the offers.
 * @param {number} [filters.maximumPrice] - The maximum price of the offers.
 * @param {string} [filters.location] - The location of the offers.
 * @param {number} [limit=5] - The maximum number of offers to fetch.
 * @param {number} [offset=0] - The number of offers to skip before starting to fetch.
 * @param {string|null} [order=null] - The order in which to fetch the offers.
 * @returns {Promise<Object|boolean>} The fetched offers data in JSON format, or false if an error occurs.
 * @throws {Error} If the response status is not OK.
 */
async function getOffers() {
    const host = window.location.protocol;
    const searchParams = new URLSearchParams();
    if (order) {
        searchParams.set("order_by", order || null);
    }
    if (filters["category"]) {
        searchParams.set("category", filters["category"] || null);
    }
    if (filters["rating"] && filters["rating"] > 0) {
        searchParams.set("rating", filters["rating"] || null);
    }
    if (filters["minimumPrice"] && filters["minimumPrice"] > 0) {
        searchParams.set("minimumPrice", filters["minimumPrice"] || null);
    }
    if (filters["maximumPrice"] && filters["maximumPrice"] > 0) {
        searchParams.set("maximumPrice", filters["maximumPrice"] || null);
    }
    if (filters["open"]) {
        searchParams.set("open", filters["open"] || null);
    }
    if (filters["minimumEventDate"]) {
        searchParams.set(
            "minimumEventDate",
            filters["minimumEventDate"] || null
        );
    }
    if (filters["maximumEventDate"]) {
        searchParams.set(
            "maximumEventDate",
            filters["maximumEventDate"] || null
        );
    }
    if (filters["location"]) {
        searchParams.set("location", filters["location"] || null);
    }
    if (filters["q"]) {
        searchParams.set("q", filters["q"] || null);
    }
    if (filters["rangePrice"]) {
        searchParams.set("rangePrice", filters["rangePrice"] || null);
    }
    let search = searchParams.toString();
    let moreSearch = "";
    if (search) {
        moreSearch = "&";
    }
    const url =
        host +
        "/api/offers" +
        "?limit=" +
        limit +
        "&offset=" +
        offset +
        moreSearch +
        search; //url of the api for research page offers's data
    console.log("url : " + url);
    try {
        const response = await fetch(url); //fetching the data from the api
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`); //if the response is not ok, throw an error
        }

        const json = await response.json(); //converting the response to json
        return json; //return the json data if all is successful
    } catch (error) {
        console.error(error.message); //logging the error message
        return false; //returning false if an error occurs
    }
}

// ---------------------------------------------------------------------------------------------- //
// Display logic
// ---------------------------------------------------------------------------------------------- //

/**
 * Applies the given filters to the current set of filters, removes any invalid filters,
 * fetches the filtered offers, and displays them.
 *
 * @param {Object} newFilters - An object containing the new filters to be applied.
 * @param {string} [newFilters.category] - The category to filter by.
 * @param {number} [filters.rating] - The minimum number of opinions for the offers.
 * @param {number} [filters.q] - The search data.
 * @param {number} [newFilters.minimumPrice] - The minimum price to filter by.
 * @param {number} [newFilters.maximumPrice] - The maximum price to filter by.
 * @param {string} [newFilters.location] - The location to filter by.
 * @returns {Promise<void>} A promise that resolves when the offers have been fetched and displayed.
 */
async function applyFilters(newFilters = {}, neworder = null) {
    filters = { ...filters, ...newFilters };
    if (neworder && neworder !== -1) {
        order = neworder;
    } else if (neworder === -1) {
        order = null;
    }
    console.log(filters);
    Object.keys(filters).forEach((key) => {
        if (
            filters[key] === null ||
            filters[key] === "" ||
            filters[key] === false ||
            filters[key] === undefined ||
            filters[key] === 0
        ) {
            delete filters[key];
        }
    });

    if (neworder || (newFilters && Object.keys(newFilters).length > 0)) {
        offset = 0;
        let Data = await getOffers();
        displayOffers(Data);
    } else {
        let Data = await getOffers();
        displayOffers(Data);
    }
    offset += 5;
}
// ---------------------------------------------------------------------------------------------- //
// SVG
// ---------------------------------------------------------------------------------------------- //
let starSVG =
    '<svg width=".8rem" height=".8rem" viewBox="0 0 10 11" fill="#000" xmlns="http://www.w3.org/2000/svg">' +
    '<path d="M7.85314 7.70782C7.86364 7.68632 7.88275 7.66457 7.90253 7.65107C7.92362 7.63668 7.95259 7.62656 7.97797 7.62372C8.00624 7.62058 8.03982 7.62505 8.06671 7.63438C8.09723 7.64497 8.12967 7.66511 8.15356 7.68686C8.1808 7.71162 8.2063 7.74662 8.22268 7.77959C8.24125 7.81701 8.25444 7.86414 8.25916 7.90565C8.26447 7.95239 8.261 8.00726 8.2509 8.05323C8.23962 8.10454 8.21663 8.16136 8.18995 8.20664C8.16041 8.25679 8.11703 8.3089 8.07375 8.34783C8.02618 8.39062 7.96372 8.43116 7.90566 8.45808C7.8423 8.48746 7.76426 8.51004 7.69512 8.51992C7.6201 8.53065 7.53204 8.52997 7.45708 8.51899C7.37621 8.50716 7.28531 8.47974 7.21101 8.44572C7.13126 8.4092 7.04577 8.35381 6.97928 8.29662C6.90823 8.23549 6.83678 8.15343 6.78532 8.07511C6.73053 7.99174 6.68133 7.88693 6.65144 7.79177C6.61971 7.69081 6.5998 7.56964 6.59673 7.46388C6.59347 7.35201 6.60793 7.22296 6.63508 7.11438C6.6637 6.99988 6.7151 6.87295 6.77351 6.77035C6.83497 6.66244 6.92293 6.54832 7.0111 6.46082C7.10364 6.369 7.22465 6.27828 7.33839 6.21451C7.4575 6.14771 7.60498 6.08994 7.7376 6.05727C7.87621 6.02314 8.04086 6.00591 8.18355 6.00983C8.33243 6.0139 8.5028 6.04213 8.64527 6.08552C8.79363 6.1307 8.95691 6.20599 9.088 6.28882C9.22432 6.37494 9.36734 6.49531 9.47602 6.61437C9.58886 6.73801 9.69904 6.89773 9.77521 7.04673C9.8542 7.20123 9.9207 7.39109 9.95611 7.56094C9.99281 7.73684 10.0074 7.94465 9.99644 8.12399C9.98508 8.30954 9.94304 8.52089 9.88325 8.69691C9.82145 8.8788 9.72207 9.07812 9.61462 9.23741C9.5037 9.40185 9.35064 9.57347 9.20039 9.70305C9.04544 9.83669 8.84664 9.96613 8.66204 10.0545C8.47181 10.1455 8.23916 10.2206 8.03171 10.2587C7.81813 10.2979 7.56673 10.3098 7.35032 10.2918C7.12773 10.2732 6.87493 10.2174 6.66496 10.1413C6.44914 10.0631 6.21336 9.93986 6.02554 9.80797C5.83261 9.67251 5.632 9.48709 5.48125 9.30597C5.32651 9.12004 5.17753 8.88261 5.0768 8.66277C4.97349 8.43727 4.88966 8.16231 4.84887 7.91769C4.81166 7.72292 4.73938 7.5047 4.6523 7.32651C4.56793 7.1539 4.44482 6.96829 4.318 6.82389C4.1953 6.68414 4.03309 6.5421 3.87792 6.4395C3.7279 6.34034 3.54046 6.24907 3.3696 6.19285C3.20464 6.13858 3.00679 6.10116 2.83334 6.09225C2.66608 6.08365 2.47252 6.09911 2.3089 6.13492C2.15136 6.16939 1.97549 6.23292 1.83266 6.3078C1.69532 6.37977 1.54839 6.48339 1.43502 6.58915C1.32618 6.69068 1.21653 6.82396 1.13855 6.95073C1.06382 7.07222 0.996427 7.22327 0.956719 7.36024C0.918725 7.49129 0.89507 7.64777 0.893422 7.7842C0.891849 7.91449 0.909986 8.06452 0.943332 8.19049C0.975107 8.31052 1.03006 8.44366 1.09289 8.55079C1.15262 8.65263 1.23697 8.76056 1.32193 8.84258C1.4025 8.92034 1.50715 8.99737 1.60575 9.05049C1.69898 9.10072 1.81399 9.14409 1.91739 9.16711C2.01483 9.18883 2.1303 9.19869 2.22997 9.19297C2.3236 9.1876 2.43046 9.16682 2.519 9.13592C2.60182 9.107 2.69259 9.06075 2.76412 9.01C2.83076 8.96274 2.89998 8.89781 2.95068 8.83377C2.99769 8.77442 3.04231 8.69857 3.07051 8.62832C3.0965 8.56355 3.11597 8.48475 3.12216 8.41526C3.12785 8.35156 3.12392 8.27726 3.11062 8.21471C3.09852 8.15781 3.07502 8.09422 3.04632 8.04359C3.0204 7.99789 2.98263 7.94957 2.94378 7.91416C2.90899 7.88245 2.86317 7.852 2.81997 7.83322C2.78164 7.81656 2.73417 7.8044 2.69243 7.80177C2.65567 7.79944 2.61258 7.804 2.57748 7.81517C2.5467 7.82499 2.513 7.84297 2.48856 7.86409C2.46705 7.88268 2.44637 7.90948 2.43496 7.93551C2.42472 7.95888 2.41902 7.98901 2.42095 8.01445C2.42275 8.03832 2.43205 8.06572 2.44546 8.08556C2.45886 8.10541 2.46816 8.1328 2.46997 8.15667C2.47191 8.18212 2.46621 8.21224 2.45597 8.23561C2.44456 8.26164 2.42388 8.28845 2.40237 8.30705C2.37793 8.32816 2.34423 8.34616 2.31344 8.35595C2.27835 8.36713 2.23526 8.37169 2.19848 8.36937C2.15676 8.36673 2.10929 8.35457 2.07096 8.3379C2.02776 8.31912 1.98194 8.28867 1.94715 8.25696C1.9083 8.22155 1.87053 8.17326 1.84461 8.12753C1.81591 8.07691 1.79241 8.01333 1.7803 7.95641C1.76699 7.89387 1.76308 7.81956 1.76875 7.75588C1.77496 7.68637 1.79441 7.60757 1.82042 7.54282C1.84862 7.47257 1.89323 7.39672 1.94025 7.33736C1.99095 7.27333 2.06017 7.20838 2.12681 7.16112C2.19834 7.11038 2.28911 7.06412 2.37193 7.03521C2.46046 7.00431 2.56733 6.98354 2.66096 6.97817C2.76063 6.97245 2.8761 6.9823 2.97354 7.00401C3.07692 7.02705 3.19195 7.07041 3.28516 7.12064C3.38378 7.17375 3.48843 7.25079 3.56899 7.32855C3.65396 7.41058 3.7383 7.51852 3.79803 7.62034C3.86087 7.72746 3.91582 7.8606 3.9476 7.98063C3.98094 8.1066 3.99908 8.25663 3.9975 8.38692C3.99586 8.52336 3.97221 8.67983 3.9342 8.81088C3.8945 8.94787 3.82711 9.09892 3.75236 9.22042C3.6744 9.34716 3.56475 9.48044 3.4559 9.58199C3.34254 9.68773 3.19561 9.79135 3.05827 9.86334C2.91544 9.9382 2.73957 10.0017 2.58201 10.0362C2.41841 10.072 2.22485 10.0875 2.05757 10.0789C1.88412 10.07 1.68629 10.0326 1.52133 9.97828C1.35047 9.92205 1.16302 9.83078 1.01302 9.73162C0.85784 9.62904 0.695625 9.48699 0.572916 9.34725C0.446116 9.20285 0.323 9.01722 0.238632 8.84461C0.15154 8.66643 0.0792716 8.44822 0.0420517 8.25345C0.00366807 8.05259 -0.00915359 7.81588 0.00654189 7.61199C0.0227146 7.40192 0.0739562 7.16311 0.144677 6.96461C0.217482 6.76026 0.332851 6.5368 0.456691 6.35862C0.584077 6.17535 0.758839 5.98454 0.929805 5.84095C1.10553 5.69336 1.33026 5.55099 1.53852 5.45446C1.75241 5.35531 2.01348 5.27446 2.24589 5.23461C2.48443 5.19371 2.76478 5.18355 3.00573 5.20633C3.2529 5.22969 3.53319 5.29465 3.76566 5.38169C3.95313 5.44687 4.17846 5.49347 4.37648 5.50721C4.5683 5.52052 4.79079 5.50684 4.97938 5.46934C5.16188 5.43305 5.36614 5.36376 5.53266 5.28081C5.69364 5.20061 5.86651 5.08409 6.00067 4.96441C6.1302 4.84884 6.26155 4.69639 6.35601 4.5508C6.4471 4.4104 6.53048 4.23522 6.58125 4.07578C6.63013 3.92224 6.66297 3.73834 6.66949 3.57736C6.67575 3.42254 6.65939 3.24363 6.62437 3.09268C6.59076 2.94775 6.53005 2.78625 6.45915 2.65542C6.39118 2.53002 6.29392 2.39619 6.19503 2.29335C6.10039 2.19495 5.97657 2.09625 5.85911 2.02661C5.74694 1.96011 5.60779 1.90078 5.48193 1.86665C5.36198 1.83412 5.21906 1.81509 5.09477 1.81588C4.97662 1.81664 4.84088 1.83564 4.72729 1.86813C4.61958 1.89895 4.50048 1.95095 4.40512 2.0097C4.31496 2.06525 4.21986 2.14306 4.1482 2.22098C4.08064 2.29442 4.01436 2.38939 3.96949 2.47846C3.92734 2.56215 3.89191 2.66499 3.87444 2.75702C3.85808 2.84313 3.85281 2.94478 3.86104 3.03204C3.86868 3.11332 3.89038 3.20566 3.92054 3.28154C3.9485 3.35189 3.99195 3.42841 4.03874 3.48793C4.0819 3.54281 4.14049 3.59903 4.19764 3.63914C4.25001 3.67591 4.31639 3.70967 4.37726 3.72943C4.43266 3.74741 4.49955 3.75888 4.55778 3.75936C4.61037 3.75981 4.67113 3.75128 4.72126 3.73537C4.76615 3.72113 4.81546 3.69671 4.85333 3.66875C4.88696 3.64391 4.92123 3.60893 4.94439 3.57414C4.96477 3.5435 4.98236 3.50395 4.99023 3.46799C4.99712 3.43646 4.99837 3.39831 4.99228 3.36661C4.98692 3.3387 4.97402 3.3074 4.95716 3.28453C4.94202 3.26398 4.91876 3.24399 4.89573 3.23294C4.87413 3.22257 4.84572 3.21693 4.82182 3.21858C4.79791 3.22026 4.76952 3.21461 4.74792 3.20423C4.72489 3.19318 4.70163 3.17319 4.68648 3.15265C4.66961 3.12977 4.65671 3.09847 4.65135 3.07056C4.64528 3.03886 4.64652 3.00072 4.65342 2.96918C4.66127 2.93324 4.67886 2.89368 4.69926 2.86302C4.72242 2.82825 4.75669 2.79328 4.7903 2.76844C4.82817 2.74046 4.87748 2.71605 4.92237 2.7018C4.9725 2.68589 5.03326 2.67738 5.08586 2.67781C5.1441 2.67829 5.21099 2.68976 5.26637 2.70774C5.32725 2.7275 5.39364 2.76127 5.44599 2.79803C5.50316 2.83815 5.56174 2.89437 5.60489 2.94925C5.65169 3.00877 5.69513 3.08529 5.72309 3.15562C5.75326 3.23152 5.77495 3.32386 5.78261 3.40515C5.79082 3.49241 5.78555 3.59404 5.76921 3.68016C5.75173 3.77219 5.71631 3.87503 5.67416 3.9587C5.62927 4.0478 5.56299 4.14275 5.49545 4.21619C5.42378 4.29412 5.32869 4.37194 5.23853 4.42747C5.14317 4.48621 5.02407 4.53824 4.91636 4.56904C4.80277 4.60154 4.66701 4.62054 4.54886 4.62129C4.42459 4.62208 4.28167 4.60305 4.16172 4.57052C4.03584 4.53639 3.8967 4.47706 3.78453 4.41056C3.66706 4.34092 3.54324 4.24222 3.44862 4.14382C3.34971 4.04097 3.25245 3.90716 3.18449 3.78174C3.1136 3.65092 3.05287 3.48942 3.01926 3.3445C2.98426 3.19356 2.9679 3.01464 2.97416 2.85983C2.98066 2.69883 3.01352 2.51493 3.0624 2.36139C3.11315 2.20196 3.19653 2.02676 3.28762 1.88637C3.38208 1.74078 3.51343 1.58833 3.64298 1.47277C3.77714 1.35307 3.95 1.23655 4.11097 1.15637C4.2775 1.07341 4.48175 1.00411 4.66426 0.967823C4.85285 0.930341 5.07533 0.916644 5.26717 0.929961C5.46519 0.94371 5.69051 0.990295 5.87797 1.05548C6.07131 1.12271 6.28294 1.22997 6.45184 1.34549C6.6259 1.46452 6.8073 1.62825 6.94404 1.78868C7.08481 1.95384 7.22084 2.16538 7.31341 2.3616C7.40861 2.56343 7.48665 2.81003 7.52566 3.02972C7.56574 3.25553 7.57681 3.52113 7.55637 3.74955C7.53538 3.98418 7.47494 4.25044 7.39329 4.47141C7.30948 4.69824 7.17812 4.94583 7.03788 5.14289C6.89405 5.34503 6.69757 5.55502 6.50589 5.71261C6.35563 5.84219 6.20258 6.01382 6.09166 6.17825C5.98419 6.33754 5.88482 6.53687 5.82304 6.71875C5.76324 6.89478 5.72119 7.10613 5.70985 7.29167C5.69887 7.47101 5.71346 7.67882 5.75015 7.85473C5.78558 8.02457 5.85207 8.21443 5.93106 8.36894C6.00722 8.51793 6.11742 8.67765 6.23027 8.80129C6.33894 8.92035 6.48196 9.04073 6.61827 9.12685C6.74936 9.20967 6.91266 9.28497 7.06102 9.33015C7.20347 9.37354 7.37384 9.40176 7.52272 9.40584C7.66543 9.40976 7.83008 9.39252 7.96868 9.35839C8.10131 9.32573 8.24879 9.26795 8.36789 9.20116C8.48164 9.13739 8.60264 9.04667 8.69517 8.95485C8.78334 8.86734 8.8713 8.75322 8.93275 8.64532C8.99118 8.54272 9.04257 8.41578 9.07121 8.30128C9.09834 8.19271 9.11281 8.06366 9.10956 7.95178C9.10648 7.84602 9.08656 7.72486 9.05485 7.6239C9.02493 7.52874 8.97576 7.42391 8.92096 7.34055C8.86948 7.26224 8.79805 7.18018 8.72698 7.11905C8.66052 7.06186 8.57503 7.00646 8.49526 6.96995C8.42098 6.93592 8.33006 6.90851 8.2492 6.89668C8.17423 6.88569 8.08616 6.88502 8.01117 6.89575C7.94202 6.90563 7.86398 6.9282 7.80061 6.95758C7.74256 6.98451 7.68011 7.02505 7.63254 7.06783C7.58924 7.10677 7.54587 7.15888 7.51632 7.20902C7.48964 7.25431 7.46666 7.31112 7.45539 7.36244C7.44529 7.4084 7.4418 7.46328 7.44712 7.51002C7.45184 7.55152 7.46502 7.59866 7.48361 7.63607C7.49996 7.66905 7.52547 7.70404 7.55272 7.72881C7.5766 7.75056 7.60906 7.7707 7.63958 7.78129C7.66645 7.79061 7.70003 7.79509 7.7283 7.79194C7.7537 7.78911 7.78265 7.77899 7.80374 7.7646C7.82354 7.75109 7.84263 7.72935 7.85314 7.70782Z"/>' +
    "</svg>";
// ---------------------------------------------------------------------------------------------- //
// Display
// ---------------------------------------------------------------------------------------------- //
let noOffersMessage = document.querySelector(".no-offers-message");

/**
 * Displays a list of offers in the offers container.
 *
 * @param {Array} Data - An array of offer objects to be displayed.
 * @param {Object} Data[].id - The unique identifier of the offer.
 * @param {Array} Data[].photos - An array of photo URLs for the offer.
 * @param {string} Data[].title - The title of the offer.
 * @param {string} Data[].category - The category of the offer.
 * @param {string} Data[].professional_id - The ID of the professional user associated with the offer.
 * @param {Object} Data[].profesionalUser - The professional user object.
 * @param {string} Data[].profesionalUser.denomination - The denomination of the professional user.
 * @param {string} Data[].summary - A brief summary of the offer.
 */
function displayOffers(Data) {
    const offersContainer = document.querySelector(".flex.flex-col.gap-2");
    if (!offersContainer) {
        console.error("Offers container not found");
    } else if (!Array.isArray(Data)) {
        console.error("Data is not an array");
    } else {
        if (offset === 0) {
            offersContainer.innerHTML = "";
        }
        if (Data.length < 5) {
            loaderSection.classList.add("hidden");
        } else {
            loaderSection.classList.remove("hidden");
        }
        if (Data.length === 0 && offset === 0) {
            noOffersMessage.classList.remove("hidden");
        } else {
            noOffersMessage.classList.add("hidden");
            Data.forEach((offer) => {
                let stars = document.createElement("div");
                stars.classList.add("stars");

                for (let i = 0; i < 5; i++) {
                    let star = document.createElement("span");
                    star.classList.add("star");
                    star.innerHTML = starSVG;

                    if (i < offer.rating) {
                        star.classList.add("fill");
                    }

                    stars.appendChild(star);
                }
                const offerElement = document.createElement("a");
                offerElement.href = `/offres/${offer.id}`;
                offerElement.innerHTML = `
                <article                 
                ${offer.relief
                    ? "class='research-card enReliefArticle mb-4 relative'"
                    : "class='research-card mb-4 relative'"
                }>
                
                
                ${offer.relief
                    ? "<img class='enReliefIcon' src='/assets/images/reliefIcon.svg' alt='Icone offre en relief'>"
                    : "<div class='hidden'></div>"
                }
                
                <div                
                ${offer.relief
                    ? "class='research-card--photo enRelief'"
                    : "class='research-card--photo'"
                }>
                
                ${
                    offer.photos[0]
                        ? `<img alt="photo d'article" src="${offer.photos[0]}" />`
                        : ""
                }
                </div>
                <div                 
                ${offer.relief
                    ? "class='research-card--body enRelief'"
                    : "class='research-card--body'"
                }">
                
                <header>
                <h2 class="research-card--title">${offer.title}</h2>

                                <div class="flex flex-row gap-2 justify-between items-center">

                <p>${translateCategory(offer.category)} par <a href="/comptes/${
                    offer.professional_id
                }" class="underline">${
                    offer.profesionalUser["denomination"]
                }</a></p>                <div class="flex flex-row gap-1 justify-center items-center">${
                    stars.outerHTML
                }
                </div></div>
                        </header>
                        <div class="flex flex-col gap-2">
                        <p class="summary">${offer.summary}</p>
                        <div class="flex flex-row gap-6">
                        ${
                            offer.minimum_price
                                ? "<div> À partir de " +
                                  offer.minimum_price +
                                  " € </div >"
                                : "<div class='hidden'></div>"
                        }
                        ${
                            offer.status
                                ? "<div>" + offer.status + "</div >"
                                : "<div class='hidden'></div>"
                        }
                        ${
                            offer.address.city
                                ? "<div>" + offer.address.city + "</div>"
                                : "<div class='hidden'></div>"
                        }
                        </div >
                        </div >
                    <div class="flex gap-2 mt-auto pt-4">
                        <a href="" class="button gray w-full spaced">Itinéraire<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map"><path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z" /><path d="M15 5.764v15" /><path d="M9 3.236v15" /></svg></a>
                        <a href="/offres/${
                            offer.id
                        }" class="button blue w-full spaced">Voir plus<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6" /></svg></a>
                    </div>
            </article >
                    `;
                offersContainer.appendChild(offerElement);
            });
        }
    }
}

function translateCategory(category) {
    switch (category) {
        case "attraction_park":
            return "parc d'attraction";
        case "visit":
            return "visite";
        case "restaurant":
            return "restaurant";
        case "activity":
            return "activité";
        case "show":
            return "spectacle";
    }
}
// ---------------------------------------------------------------------------------------------- //
// Pop up
// ---------------------------------------------------------------------------------------------- //

let popup = document.getElementById("popup");
let filterButton = document.getElementById("filterButton");
const popupContent = document.querySelector(".popup-content");

filterButton.addEventListener("click", () => {
    popup.classList.toggle("hidden");
});

popup.addEventListener("click", (event) => {
    if (!popupContent.contains(event.target)) {
        popup.classList.add("hidden");
    }
});

// ---------------------------------------------------------------------------------------------- //
// Category filter
// ---------------------------------------------------------------------------------------------- //

let categoryListenners = [
    "spectacles",
    "restauration",
    "visites",
    "activités",
    "attractions",
];
let categoryValue = [
    "show",
    "restaurant",
    "visit",
    "activity",
    "attraction_park",
];

/**
 * Selects all elements with the class "category-item" and assigns them to the variable `categories`.
 *
 * @type {NodeListOf<Element>}
 */
categoryListenners.forEach((listener, index) => {
    const element = document.getElementById(listener);
    let categories = document.querySelectorAll(".category-item");
    if (element) {
        element.addEventListener("click", () => {
            if (element.classList.contains("active")) {
                element.classList.remove("active");
                applyFilters({ category: null });
            } else {
                categories.forEach((cat) => cat.classList.remove("active"));
                element.classList.add("active");
                applyFilters({ category: categoryValue[index] });
            }
        });
    } else {
        console.warn(`Element with ID ${listener} not found`);
    }
});
// ---------------------------------------------------------------------------------------------- //
// listeners
// ---------------------------------------------------------------------------------------------- //
function debounce(func, wait) {
    let timeout;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

let searchInput = document.querySelector(".search-input");

searchInput.addEventListener(
    "input",
    debounce(() => {
        let value = searchInput.value.trim();
        applyFilters({ q: value });
    }, 300)
);

let searchCity = document.querySelector(".searchCity");

searchCity.addEventListener(
    "input",
    debounce(() => {
        let value = searchCity.value.trim();
        applyFilters({ location: value });
    }, 300)
);

const sliderPrice = document.getElementById("slider-price");

sliderPrice.addEventListener(
    "slider-change",
    debounce((event) => {
        let { minValue, maxValue } = event.detail;
        if (maxValue == 50) {
            maxValue = null;
        }
        applyFilters({ minimumPrice: minValue, maximumPrice: maxValue });
    }, 300)
);

const sliderRating = document.getElementById("slider-rating");

sliderRating.addEventListener(
    "slider-change",
    debounce((event) => {
        const { minValue } = event.detail;
        applyFilters({ rating: minValue });
    }, 300)
);

// ---------------------------------------------------------------------------------------------- //
// SearchBar
// ---------------------------------------------------------------------------------------------- //
const urlParams = new URLSearchParams(window.location.search);
const searchQuery = urlParams.get('search');
if (searchQuery) {
    searchInput.value = searchQuery;
    filters = { q: searchQuery };
}
// const switchInput = document.getElementById("switchtest");

// switchInput.addEventListener("change", async (event) => {
//     await applyFilters({ open });
// });

// const dateBeforeInput = document
//     .querySelector(".dateBefore")
//     .closest("x-input");
// const dateAfterInput = document.querySelector(".dateAfter").closest("x-input");

// dateBeforeInput.addEventListener("dateChange", (event) => {
//     console.log(event.detail.value);
//     applyFilters({ minimumEventDate: event.detail.value });
// });

// dateAfterInput.addEventListener("dateChange", (event) => {
//     console.log(event.detail.value);

//     applyFilters({ maximumEventDate: event.detail.value });
// });
let sort = document.getElementById("sort");
sort.addEventListener("change", (event) => {
    const inputValue = event.target.querySelector("input").value;
    const selectedOption = sort.querySelector(`[data-value="${inputValue}"]`);
    const dataValue = selectedOption
        ? selectedOption.getAttribute("data-value")
        : null;
    if (dataValue === "croissantPrice") {
        console.log("Tri par prix croissant");
        applyFilters({}, "price_asc");
    } else if (dataValue === "decroissantPrice") {
        console.log("Tri par prix décroissant");
        applyFilters({}, "price_desc");
    } else if (dataValue === "croissantRating") {
        console.log("Tri par note croissante");
        applyFilters({}, "rating_asc");
    } else if (dataValue === "decroissantRating") {
        console.log("Tri par note décroissante");
        applyFilters({}, "rating_desc");
    } else if (dataValue === "reset") {
        console.log("Reset des filtres");
        applyFilters({}, -1);
    }
});

let filterRangePriceRestau = document.getElementById("filterRangePriceRestau");
filterRangePriceRestau.addEventListener("change", (event) => {
    const inputValue = event.target.querySelector("input").value;
    const selectedOption = filterRangePriceRestau.querySelector(
        `[data-value="${inputValue}"]`
    );
    const dataValue = selectedOption
        ? selectedOption.getAttribute("data-value")
        : null;
    if (dataValue === "1") {
        console.log("Filtre prix moins de 25");
        applyFilters({ rangePrice: 1 });
    }
    if (dataValue === "2") {
        console.log("Filtre prix entre 25 et 40");
        applyFilters({ rangePrice: 2 });
    }
    if (dataValue === "3") {
        console.log("Filtre prix superieur a 40");
        applyFilters({ rangePrice: 3 });
    }
});
// ---------------------------------------------------------------------------------------------- //
// Observer
// ---------------------------------------------------------------------------------------------- //

let loaderSection = document.querySelector("#loader-section");

async function loadOffers() {
    await applyFilters();
}

// Create intersection observer on loader section
const observer = new IntersectionObserver(
    (entries) => {
        if (entries[0].isIntersecting) {
            loadOffers();
        }
    },
    {
        root: null,
        rootMargin: "0px",
        threshold: 1,
    }
);

observer.observe(loaderSection);

