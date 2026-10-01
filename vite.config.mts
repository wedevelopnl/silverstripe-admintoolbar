/// <reference types="vitest/config" />

import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { defineConfig } from 'vite'

const __dirname = dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  build: {
    outDir: 'client/dist',
    emptyOutDir: true,
    // vendor-plugin symlinks public/_resources back into client/dist; copying a
    // public dir into it would recurse.
    copyPublicDir: false,
    lib: {
      entry: resolve(__dirname, 'client/src/ts/toolbar.ts'),
      name: 'AdminToolbar',
      formats: ['iife'],
      fileName: () => 'js/toolbar.js',
    },
  },
  test: {
    environment: 'jsdom',
    globals: true,
    include: ['client/src/ts/**/*.test.ts'],
    setupFiles: ['./vitest.setup.ts'],
    css: false,
    coverage: {
      include: ['client/src/ts/**/*.ts'],
      exclude: [
        'client/src/ts/toolbar.ts',
        'client/src/ts/**/*.test.ts',
        'client/src/ts/testing/**',
      ],
      thresholds: {
        statements: 90,
        branches: 85,
        functions: 90,
        lines: 90,
      },
    },
  },
})
