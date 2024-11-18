/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './html/**/*.{php,js}',
        './views/**/*.php'
    ],
    theme: {
        extend: {
            colors: {
                'blue-primary': 'rgb(0, 87, 255)',
                'blue-secondary': 'rgb(0, 162, 255)',
                'blue-tertiary': 'rgb(0, 212, 249)',
                'blue-light': 'rgb(230, 239, 255)',
                'orange-primary': 'rgb(255, 168, 0)',
                'orange-secondary': 'rgb(255, 193, 78)',
                'orange-tertiary': 'rgb(255, 215, 132)',
                'purple-primary': 'rgb(201, 51, 231)',
                'purple-light': 'rgb(250, 235, 253)',
                'footer': 'rgb(255, 216, 132)',
                'danger': 'rgb(255, 59, 48)',
                'warning': 'rgb(255, 204, 0)',
                'success': 'rgb(52, 199, 89)',
                'white': 'rgb(255, 255, 255)',
                'gray-1': 'rgb(225, 225, 225)',
                'gray-2': 'rgb(187, 187, 187)',
                'gray-3': 'rgb(131, 131, 131)',
                'gray-4': 'rgb(82, 82, 82)',
                'black': 'rgb(31, 31, 31)',
            }
        },
    },
    plugins: [],
}
