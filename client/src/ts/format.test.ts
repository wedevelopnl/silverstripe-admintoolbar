import { format } from './format'

describe('format', () => {
  it('replaces every occurrence of each token', () => {
    expect(format('{a} and {b}, then {a}', { a: 'x', b: 'y' })).toBe('x and y, then x')
  })

  it('leaves unknown tokens literal', () => {
    expect(format('{ms} ms ({count} queries)', { ms: 5 })).toBe('5 ms ({count} queries)')
  })

  it('does not resolve tokens from the object prototype', () => {
    expect(format('{constructor}', {})).toBe('{constructor}')
  })

  it('stringifies numbers', () => {
    expect(format('{ms} ms', { ms: 0 })).toBe('0 ms')
  })
})
