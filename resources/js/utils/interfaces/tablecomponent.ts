import { VNode } from 'vue'

export interface TitleColumn {
  title: string
  classname?: string
}

export interface DataColumn {
  element: string | VNode
  classname?: string
}

export interface Row {
  classname?: string
  columns: DataColumn[]
}
