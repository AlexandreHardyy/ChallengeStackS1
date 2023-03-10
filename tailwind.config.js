/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  darkMode: 'class', // or 'media' or 'class'
    theme: {
        fontFamily: {
            'bungee-inline': ['Bungee Inline', 'cursive'],
            'bungee-shade': ['Bungee Shade', 'cursive'],
            'lato': ['Lato', 'sans-serif']
        },
        extend: {
          colors: {
            transparent: 'transparent',
            current: 'currentColor',
            'main-blue': '#F8F9FD',
            'main-blue-dark': '#38537C',
            'purple': '#3f3cbb',
            'main-grey': '#B3B3B3',
            'midnight': '#121063',
            'metal': '#565584',
            'tahiti': '#3ab7bf',
            'silver': '#ecebff',
            'bubble-gum': '#ff77e9',
            'bermuda': '#78dcca',
          },
        },
    },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
  ],
}