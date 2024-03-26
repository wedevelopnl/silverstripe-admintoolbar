import {themeSafeList} from './client/src/js/tailwind/theme-safelist';
import {themeFontSizes, themeFontFamily} from './client/src/js/tailwind/theme-typography';
import {themeColors} from './client/src/js/tailwind/theme-colors';
import {themeDimensions} from "./client/src/js/tailwind/theme-dimensions";

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./templates/**/*.{html,js,ss}"],
  prefix: 'ss-at-',
  plugins: [
    require('@tailwindcss/typography'),
  ],
  corePlugins: {
    preflight: false,
  },
  theme: {
    extend: { // this will add extra classes, if you want to overwrite the default classes for a specific "property", move it outside the "extend"
      colors: themeColors,
    },
    fontSize: themeFontSizes,
    fontFamily: themeFontFamily,
  },
  safelist: themeSafeList,
}
