interface Props {
  _storedScrollTop: number
  _storedChildCount: number
  _onScroll: () => void
}
  
const vScrollPreserver = {
  mounted(el: HTMLElement & Props) {
    el._storedScrollTop = el.scrollTop
    el._storedChildCount = el.children.length
  
    el._onScroll = () => {
      el._storedScrollTop = el.scrollTop
    }
  
    el.addEventListener('scroll', el._onScroll)
  },
  updated(el: HTMLElement & Props) {
    const oldChildCount = el._storedChildCount
    const newChildCount = el.children.length
  
    if (newChildCount > oldChildCount) {
      el.scrollTop = el._storedScrollTop
    }
  
    el._storedChildCount = newChildCount
  },
  unmounted(el: HTMLElement & Props) {
    el.removeEventListener('scroll', el._onScroll)
  },
}
  
export default vScrollPreserver
