/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    '!./vendor/**',
    '!./**/_*',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}