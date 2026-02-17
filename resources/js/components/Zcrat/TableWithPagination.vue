<script setup lang="ts" generic="T extends Record<string, any>">
import AppLayout from '@/layouts/AppLayout.vue';
import {toRefs,computed} from 'vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import {DataColumn} from '@/types/tablecomponent'
import Table from '@/components/Zcrat/Elements/Table.vue'
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
interface Props {
    titlePage: string;
    titles: string[];
    keys: string[];
    api: string;
    params?:Record<string, any>;
    HiddenTitleOptions?:boolean;
    HiddenTitleActions?:boolean;
    Options?:(item: T) => DataColumn ;
    Actions?:(item: T) => DataColumn ;
}
const props = defineProps<Props>();

const {params,api,keys,titles,Options,Actions,HiddenTitleOptions,HiddenTitleActions} = toRefs(props)

const stableParams = computed(() => ({
    search: search.value,
    ...params.value
}));
const loading = defineModel<boolean>('loading', { default: false })
const currentPage = defineModel<number>('currentPage', { default: 1 })
const itemsPerPage = defineModel<number>('itemsPerPage', { default: 10 })
const search = defineModel<string>('search', { default: '' })
const elements = defineModel<T[]>('elements', { default: [] })
const emptymessage=defineModel<string>('emptymessage', { default: 'Sin Resultados' })
</script>
<template>
    <AppLayout :title="titlePage">
            <template #filtering>
                <div class="flex flex-row justify-start py-4 w-full">
                    <slot v-if="$slots.filtering1" name="filtering1"/>
                    <Search Classdiv="sm:w-[20rem] w-full" v-model="search"/>
                    <slot v-if="$slots.filtering1" name="filtering2"/>
                </div>
            </template>
            <template #content>
                <Pagination :api="api" :params="stableParams" v-model:currentPage="currentPage" v-model:itemsPerPage="itemsPerPage" v-model:loading="loading"/>
                
                <Table v-if="elements.length > 0" :titles="[(Options && !HiddenTitleOptions) ? 'Opciones':null,...titles,(Actions && !HiddenTitleActions ) ? 'Acciones':null].filter((item)=> item!=null)"
                    :rows="elements.map(function(row){return {
                        classname:'bg-grey-300',
                        columns: [Options? Options(row) :null,...keys.map((key)=> row[key]),Actions?Actions(row):null].filter((item)=> item!=null)
                    }})" 
                    
                    classname="tabla"></Table>
                <div v-else class="flex w-full h-full justify-center items-center">
                    <h3>{{emptymessage}}</h3>
                </div>
            </template>
    </AppLayout>
</template>