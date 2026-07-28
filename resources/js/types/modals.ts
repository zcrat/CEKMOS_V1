export interface ConfirmButton {
    text:string
    className?: string
    disabled?:boolean
    onClick: (event: MouseEvent) => void
}
