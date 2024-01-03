import {themeSafeList} from './client/src/js/tailwind/theme-safelist';
import {themeFontSizes} from './client/src/js/tailwind/theme-typography';
import {themeSpacings} from './client/src/js/tailwind/theme-spacings';
import {themeColors} from './client/src/js/tailwind/theme-colors';
import {themeDimensions} from "./client/src/js/tailwind/theme-dimensions";

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./templates/**/*.{html,js,ss}"],
  plugins: [
    require('@tailwindcss/typography'),
  ],
  theme: {
    extend: { // this will add extra classes, if you want to overwrite the default classes for a specific "property", move it outside the "extend"
      colors: themeColors,
    },
    fontSize: themeFontSizes,
    spacing: themeSpacings,
  },
  safelist: themeSafeList,
}
