import {themeFontSizes, themeFontFamily} from './client/src/js/tailwind/theme-typography';
import {themeColors} from './client/src/js/tailwind/theme-colors';
import {
  scopedPreflightStyles,
  isolateInsideOfContainer,
} from 'tailwindcss-scoped-preflight';

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.ss',
    './client/src/js/*.js',
  ],
  prefix: 'ss-at-',
  plugins: [
    scopedPreflightStyles({
      isolationStrategy: isolateInsideOfContainer('#admin-toolbar'),
    }),
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
  safelist: [
    'ss-at-text-priamry',
    'hover:ss-at-text-primary',
    'ss-at-col-span-1',
    'ss-at-col-span-2',
    'ss-at-col-span-3',
    'ss-at-col-span-4',
    'ss-at-col-span-5',
    'ss-at-col-span-6',
    'ss-at-col-span-7',
    'ss-at-col-span-8',
    'ss-at-col-span-9',
    'ss-at-col-span-10',
    'ss-at-col-span-11',
    'ss-at-col-span-12',
    'ss-at-collapse',
    'ss-at-rotate-180',
    'ss-at-origin-center',
    'ss-at-bg-blue-200',
    'ss-at-bg-blue-800',
    'ss-at-bg-orange-200',
    'ss-at-bg-orange-800',
    'ss-at-bg-green-200',
    'ss-at-bg-green-800',
    'ss-at-bg-yellow-200',
    'ss-at-bg-yellow-800',
    'ss-at-text-red-600',
    'hover:ss-at-text-red-700',
    'peer-hover:ss-at-opacity-100',
  ],
};
