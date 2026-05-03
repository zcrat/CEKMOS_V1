<script setup lang="ts">
import { TitleColumn, Row ,OrderKeyProp,OrderKeysProp} from '@/types/tablecomponent'
import TitleOrderBy from '../Filters/TitleOrderBy.vue'
import { computed } from 'vue';
import { forEach } from 'lodash';


const props = defineProps<{
  classname?: string
  rows: Row[]
  titles: (TitleColumn | string)[]
}>()
interface TittleArray{
  title:string,
  row:number,
  cols:number,
  CanOrder?:OrderKeysProp,
  classname?:string
}

const OrderKey = defineModel<OrderKeyProp | null>('OrderKey',{default:null})

const OnClickOrder= (Order:OrderKeysProp)=>{
  if(OrderKey.value == null || OrderKey.value.key !== Order.key){
    const orderby=Order.types == 'ambos' ? 'asc' : Order.types;
    OrderKey.value =  {
        key:Order.key,
        order:orderby
    }
  }else {
    const orderby=Order.types == 'ambos' ? (OrderKey.value.order == 'asc' ? 'desc' : null ) : null;
    if(orderby){
      OrderKey.value =  {
        key:Order.key,
        order:orderby
      }
    }else{
      OrderKey.value=null;
    }
    
  }
}
const titlesComputed = computed(() => {
  const localtitles: TittleArray[] = []
  const subtittles: TittleArray[] = []

  const anyhas = props.titles.some(
    item => typeof item !== 'string' && item.subtittles
  )

  props.titles.forEach((title) => {
    if (typeof title === 'string') {
      localtitles.push({
        title,
        row: anyhas ? 2 : 1,
        cols: 1
      })
    } else {
      localtitles.push({
        title: title.title,
        row: anyhas ? (title.subtittles ? 1 : 2) : 1,
        cols: title.subtittles?.length ?? 1,
        CanOrder: title.CanOrder
      })

      if (title.subtittles) {
        title.subtittles.forEach((subtitle) => {
          subtittles.push({
            title: subtitle.title,
            row: 1,
            cols: 1,
            CanOrder: title.CanOrder
          })
        })
      }
    }
  })

  return {
    SimpleTitles: localtitles,
    Subtittles: subtittles
  }
})

</script>


<template>
  <div class="rounded w-content w-full p-2">
    <table :class="['w-auto sm:w-full', classname]">
      <thead>
        <tr>
          <th
            v-for="(col, index) in titlesComputed.SimpleTitles"
            :key="index"
            :class="col.classname"
            :rowspan="col.row"
            :colspan="col.cols"
          >
            <TitleOrderBy
              v-if="col.CanOrder != undefined"
              @click="()=>{
                if(typeof col !== 'string' && col.CanOrder != undefined){
                  OnClickOrder(col.CanOrder)
                }
              }"
              :title="col.title"
              :order="OrderKey?.key == col.CanOrder.key
                        ? OrderKey.order 
                        : null"
            />
            <span v-else>
              {{ col.title }}
            </span>
          </th>
        </tr>
        <tr v-if="titlesComputed.Subtittles.length>0">
          <th
            v-for="(col, index) in titlesComputed.Subtittles"
            :key="index"
            :class="col.classname"
            :rowspan="col.row"
            :colspan="col.cols"
          >
            <TitleOrderBy
              v-if="col.CanOrder != undefined"
              @click="()=>{
                if(typeof col !== 'string' && col.CanOrder != undefined){
                  OnClickOrder(col.CanOrder)
                }
              }"
              :title="col.title"
              :order="OrderKey?.key == col.CanOrder.key
                        ? OrderKey.order 
                        : null"
            />
            <span v-else>
              {{ col.title }}
            </span>
          </th>

        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(row, index) in rows"
          :key="'row_' + index"
          :class="row.classname"
        >
          <td
            v-for="(col, colnum) in row.columns"
            :key="'col_' + index + '_' + colnum"
            :class=" typeof col === 'object' ? col.classname : ' uppercase'"
          >
          <span v-if="(typeof col !== 'object')" v-html="col"></span>
          
          <span v-else-if="(typeof col.element !== 'object')" v-html="col.element"></span>

          <component
              v-else
              :is="col.element"
              v-bind="col.props || {}"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
