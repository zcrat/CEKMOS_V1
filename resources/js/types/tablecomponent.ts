import { Component, VNode } from 'vue'

export interface TitleColumn {
  title: string
  classname?: string
}

export interface DataColumn {
  element: string | Component
  props?: Record<string, any>
  classname?: string
}

export interface Row {
  classname?: string
  columns: DataColumn[]
}
export interface  ComponentInterface {
    element: Component;
    props?: Record<string, any>;
    }
