import { type DirectiveBinding } from 'vue'

function initDynamicHeightCalc(input: HTMLTextAreaElement & { _calculateDynamicHeight: () => void }, binding?: DirectiveBinding<boolean>) {
  input.style.resize = 'none'
  input._calculateDynamicHeight = () => calculateDynamicHeight(input, binding)

  document.fonts.addEventListener('loadingdone', input._calculateDynamicHeight)
  input.addEventListener('transitionend', input._calculateDynamicHeight)

  input._calculateDynamicHeight()
}

function calculateDynamicHeight(input: HTMLTextAreaElement, binding?: DirectiveBinding<boolean>){
  if (binding?.value === false) {
    return
  }

  // Reset the height to a minimal value to refresh scrollHeight.
  // This ensures the textarea will shrink when text is removed.
  input.style.height = '0'
  input.style.height = `${input.scrollHeight+1}px`
}

function removeDynamicHeightCalc(input: HTMLTextAreaElement & { _calculateDynamicHeight: () => void }) {
  document.fonts.removeEventListener('loadingdone', input._calculateDynamicHeight)
  input.removeEventListener('transitionend', input._calculateDynamicHeight)
}

const vDynamicTextAreaHeight = {
  mounted: initDynamicHeightCalc,
  updated: calculateDynamicHeight,
  unmounted: removeDynamicHeightCalc,
}

export default vDynamicTextAreaHeight
