/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    '../*.php',
    './frontend/**/*.{php,html,js}',
    '!./vendor/**',
    '!./_*',
    '!./frontend/_*',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
