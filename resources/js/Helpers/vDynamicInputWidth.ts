import { type DirectiveBinding } from 'vue'

const canvas = document.createElement('canvas')

function initDynamicWidthCalc(input: HTMLInputElement & { _calculateDynamicWidth: () => void }, binding?: DirectiveBinding<boolean>) {
  input._calculateDynamicWidth = () => calculateDynamicWidth(input, binding)
  document.fonts.addEventListener('loadingdone', input._calculateDynamicWidth)
  input.addEventListener('transitionend', input._calculateDynamicWidth)
  input._calculateDynamicWidth()
}

function calculateDynamicWidth(input: HTMLInputElement, binding?: DirectiveBinding<boolean>){
  if (binding?.value === false) {
    return
  }

  const context = canvas.getContext('2d')

  if (!context) {
    return
  }

  const { fontWeight, fontSize, fontFamily } = window.getComputedStyle(input)
  context.font = `${fontWeight} ${fontSize} ${fontFamily}`
  const width = context.measureText(input.value || input.placeholder).width

  input.style.minWidth = `${width}px`
  input.style.width = `clamp(1.1rem,${width}px,100%)`
}

function removeDynamicWidthCalc(input: HTMLInputElement & { _calculateDynamicWidth: () => void }) {
  document.fonts.removeEventListener('loadingdone', input._calculateDynamicWidth)
  input.removeEventListener('transitionend', input._calculateDynamicWidth)
}

const vDynamicInputWidth = {
  mounted: initDynamicWidthCalc,
  updated: calculateDynamicWidth,
  unmounted: removeDynamicWidthCalc,
}

export default vDynamicInputWidth
