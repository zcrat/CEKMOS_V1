import { Component, VNode } from 'vue'

export interface TitleColumn {
  title: string
  classname?: string
  CanOrder?:OrderKeysProp
}

export interface DataColumn {
  element: string| number| Component
  props?: Record<string, any>
  classname?: string
}

export interface Row {
  classname?: string
  columns: (DataColumn | string | number )[]
}
export interface  ComponentInterface {
    element: Component;
    props?: Record<string, any>;
    }

export interface OrderKeyProp {
    key:string
    order:'desc'|'asc'
}
export interface OrderKeysProp {
    key:string
    types:'desc'|'asc'|'ambos'
}