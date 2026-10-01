/**
 * localStorage can throw (private browsing, blocked storage). The toolbar only keeps
 * conveniences there, so a failure reads as "nothing stored" and a write is dropped.
 */
export function readItem(key: string): string | null {
  try {
    return window.localStorage.getItem(key)
  } catch {
    return null
  }
}

export function writeItem(key: string, value: string): void {
  try {
    window.localStorage.setItem(key, value)
  } catch {
    // Dropped on purpose: see the module comment.
  }
}
