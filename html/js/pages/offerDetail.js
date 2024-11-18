

// ---------------------------------------------------------------------------------------------- //
// Map
// ---------------------------------------------------------------------------------------------- //

let lannion = [48.73218569636991, -3.4613401408248046];

let map = L.map('map', {
    center: lannion,
    zoom: 10
});

let tileLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

let marker = L.marker(lannion).addTo(map);


// ---------------------------------------------------------------------------------------------- //
// Header
// ---------------------------------------------------------------------------------------------- //

let header = document.querySelector('.page-header');

if (header) {
    let headerTop = header.offsetTop;

    window.addEventListener('scroll', function () {
        if (window.scrollY > headerTop + 16 * 2) {
            header.classList.add('touched');
        } else {
            header.classList.remove('touched');
        }
    });
}