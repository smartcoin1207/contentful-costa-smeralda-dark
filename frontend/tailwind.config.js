/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    '../*.php',
    './src/**/*.{php,html,js}',
    '!./vendor/**',
    '!./_*',
    '!./frontend/_*',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
