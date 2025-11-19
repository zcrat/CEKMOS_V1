<script setup lang="ts">
import { defineProps } from 'vue'
import { TitleColumn, Row } from '@/types/tablecomponent'


defineProps<{
  classname?: string
  rows: Row[]
  titles: TitleColumn[]
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
          :class="col.classname"
        >
          {{ col.title }}
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
          :class="col.classname"
        >
        <span v-if="typeof col.element === 'string'" v-html="col.element"></span>

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
