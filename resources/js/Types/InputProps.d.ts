export interface InputProps {
    // there is no `value` property, due to the conflicts with `v-model`
    accept?: string
    alt?: string
    autocomplete?: string
    autofocus?: Booleanish
    capture?: boolean | 'user' | 'environment'
    checked?: Booleanish | any[] | Set<any>
    crossorigin?: string
    disabled?: Booleanish
    enterKeyHint?: 'enter' | 'done' | 'go' | 'next' | 'previous' | 'search' | 'send'
    form?: string
    formaction?: string
    formenctype?: string
    formmethod?: string
    formnovalidate?: Booleanish
    formtarget?: string
    height?: Numberish
    indeterminate?: boolean
    list?: string
    max?: Numberish
    maxlength?: Numberish
    min?: Numberish
    minlength?: Numberish
    multiple?: Booleanish
    name?: string
    pattern?: string
    placeholder?: string
    readonly?: Booleanish
    required?: Booleanish
    size?: Numberish
    src?: string
    step?: Numberish
    type?: InputTypeHTMLAttribute
    width?: Numberish
} 
