<script setup lang="ts">
import { TitleColumn, Row ,OrderKeyProp,OrderKeysProp} from '@/types/tablecomponent'
import TitleOrderBy from '../Filters/TitleOrderBy.vue'


defineProps<{
  classname?: string
  rows: Row[]
  titles: (TitleColumn | string)[]
}>()

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

</script>


<template>
  <div class="rounded w-content w-full">
    <table :class="['w-auto sm:w-full', classname]">
      <thead>
        <tr>
          <th
            v-for="(col, index) in titles"
            :key="index"
            :class="typeof col !== 'string' ? col.classname : ''"
            
          >
            <template v-if="(typeof col !== 'string')" >
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
          </template>
          <template v-else>
            {{ col }}
          </template>
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
