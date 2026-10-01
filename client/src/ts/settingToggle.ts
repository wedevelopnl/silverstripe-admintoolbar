import { readItem, writeItem } from './storage'

export type SettingHook = 'data-queries-toggle' | 'data-timing-toggle'

/** The 2.x key (`[<hook>]`, `'true'` = enabled), so members' choices survive the upgrade. */
const storageKey = (hook: SettingHook): string => `[${hook}]`

export function isSettingEnabled(hook: SettingHook): boolean {
  return readItem(storageKey(hook)) === 'true'
}

export function initSettingToggle(
  hook: SettingHook,
  root: ParentNode = document,
  reload: () => void = () => window.location.reload(),
): void {
  const checkbox = root.querySelector<HTMLInputElement>(`input[${hook}]`)
  if (!checkbox) {
    return
  }

  checkbox.checked = isSettingEnabled(hook)
  checkbox.addEventListener('change', () => {
    writeItem(storageKey(hook), String(checkbox.checked))
    reload()
  })
}
