import { initCollapse } from './collapse'
import { initDialogs } from './dialogs'
import { initFlushCache } from './flushCache'
import { initPageActions } from './pageActions'
import { initQueries } from './queries'
import { initSettingToggle } from './settingToggle'
import { initTiming } from './timing'

function init(): void {
  initCollapse()
  initDialogs()
  initPageActions()
  initFlushCache()
  initSettingToggle('data-queries-toggle')
  initSettingToggle('data-timing-toggle')
  void initQueries()
  initTiming()
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init)
} else {
  init()
}
