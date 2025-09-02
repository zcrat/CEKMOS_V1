<script setup lang="ts">
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue'
  import Poper from '@/components/Zcrat/Elements/poper.vue' 
</script>
<template>
    <poper :father="'Fechas'" :classname="'ml-2'"
                    :stoppropagation="true"
                    :align="'left'"
                    :children="[
                        {
                            element: Checkbox,
                            props: {
                            checked: true,
                            id:'pedientes',
                            name:'pedientes',
                            label: 'Pendientes',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        },{
                            element: Checkbox,
                            props: {
                            checked: true,
                            id:'pedientes',
                            name:'pedientes',
                            label: 'Pendientes',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        },
                        {
                            element: Checkbox,
                            props: {
                            checked: true,
                            id:'pedientes',
                            name:'pedientes',
                            label: 'Pendientes',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        },{
                            element: Checkbox,
                            props: {
                            checked: true,
                            id:'pedientes',
                            name:'pedientes',
                            label: 'Pendientes',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        }
                        ]"

                />
</template>