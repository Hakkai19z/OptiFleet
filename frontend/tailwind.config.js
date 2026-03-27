/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#534AB7',
          50: '#EEEDF9',
          100: '#DDDCF4',
          200: '#BBB9E8',
          300: '#9997DD',
          400: '#7774D1',
          500: '#534AB7',
          600: '#433B92',
          700: '#332C6E',
          800: '#221E49',
          900: '#110F25',
        },
        blue: {
          fleet: '#185FA5',
        },
        teal: {
          fleet: '#0F6E56',
        },
        amber: {
          fleet: '#854F0B',
        },
        danger: {
          DEFAULT: '#A32D2D',
          light: '#FEE2E2',
        },
        dark: '#2C2C2A',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      spacing: {
        '18': '4.5rem',
        '72': '18rem',
        '80': '20rem',
        '88': '22rem',
      },
      screens: {
        'sm': '640px',
        'md': '768px',
        'lg': '1280px',
        'xl': '1440px',
      },
    },
  },
  plugins: [],
}
