<script setup>
import Search from '@/components/Zcrat/Inputs/Search.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const titlestable=[
    {title:'Opciones',classname:''},
    {title:'Nombre',classname:''},
    {title:'Fecha',classname:''}
]
const rowstable=[
    {columns:[{
        html:'<div><h2>holis</h2></div>',
        classname:''
    },{
        html:'holis',
        classname:''
    },{
        html:'holis',
        classname:''
    }],classname:''},
     {columns:[{
        html:'holis',
        classname:''
    }],classname:''},
     {columns:[{
        html:'holis',
        classname:''
    }],classname:''},
]
</script>

<template>
    <AppLayout title="Usuarios" description="Bienvenido al sistema CEKMOS">
        <template #header>
        </template>

        <div class="h-full flex flex-col items-center justify-center sm:px-4">
            <div class="flex flex-row justify-start py-4  w-full">
                <Search Classdiv="sm:w-[20rem] w-full"/>
            </div>
            <div class="flex h-full w-full p-8">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th v-for="(col, index) in titlestable" :key="index" :class="col.classname">
                            {{ col.title }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in rowstable" :key="'row_'+index" :class="row.classname">
                            <td v-for="(col, colnum) in row.columns" :key="'col_'+index+'_'+colnum" :class="col.classname" v-html="col.html">

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
