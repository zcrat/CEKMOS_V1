<script setup lang="ts">
import { TitleColumn, Row } from '@/types/tablecomponent'


defineProps<{
  classname?: string
  rows: Row[]
  titles: (TitleColumn | string)[]
}>()
</script>

<template>
  <div class="rounded w-content w-full">

  <table :class="['w-auto sm:w-full', classname]">
    <thead>
      <tr>
        <th
          v-for="(col, index) in titles"
          :key="index"
          :class="typeof(col)!=='string' ? col.classname : ''"
        >
          {{ typeof(col)!=='string' ? col.title : col}}
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
