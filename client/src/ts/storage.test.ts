import { readItem, writeItem } from './storage'

describe('storage', () => {
  it('reads back what it wrote', () => {
    writeItem('key', 'value')

    expect(readItem('key')).toBe('value')
  })

  it('reads null when nothing is stored', () => {
    expect(readItem('missing')).toBeNull()
  })

  it('reads null when localStorage throws', () => {
    vi.spyOn(localStorage, 'getItem').mockImplementation(() => {
      throw new DOMException('denied', 'SecurityError')
    })

    expect(readItem('key')).toBeNull()
  })

  it('drops the write when localStorage throws', () => {
    vi.spyOn(localStorage, 'setItem').mockImplementation(() => {
      throw new DOMException('full', 'QuotaExceededError')
    })

    expect(() => writeItem('key', 'value')).not.toThrow()
  })
})
