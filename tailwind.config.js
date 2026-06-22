/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./pages/**/*.php", "./components/**/*.php", "./**/*.html", "./assets/**/*.js"],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        serif: ['"Plus Jakarta Sans"', 'sans-serif'],
      }
    },
  },
  plugins: [],
}

