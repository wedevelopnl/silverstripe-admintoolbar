/** @type {import('@stryker-mutator/api/core').PartialStrykerOptions} */
export default {
  testRunner: 'vitest',
  mutate: [
    'client/src/ts/**/*.ts',
    '!client/src/ts/**/*.test.ts',
    '!client/src/ts/toolbar.ts',
    '!client/src/ts/testing/**',
  ],
  ignorePatterns: ['public', 'vendor'],
  checkers: ['typescript'],
  tsconfigFile: 'tsconfig.json',
  reporters: ['progress', 'clear-text', 'json'],
  jsonReporter: {
    fileName: 'reports/mutation/mutation.json',
  },
  thresholds: {
    high: 85,
    low: 75,
    break: 75,
  },
}
