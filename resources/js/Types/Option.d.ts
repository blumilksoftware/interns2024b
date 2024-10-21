export default interface Option{
  key: number | string
  title? : string
  text: string
  action?: ()=>void
}
