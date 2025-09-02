<script setup lang="ts">
  import Checkbox from '@/components/Zcrat/Inputs/form/Checkbox.vue'
  import Poper from '@/components/Zcrat/Elements/poper.vue' 
</script>
<template>
    <poper :father="'Estatus'" :classname="''"
                    :stoppropagation="true"
                    :align="'left'"
                    :children="[
                        {
                            element: Checkbox,
                            props: {
                            id:'pedientes',
                            checked: true,
                            name:'pedientes',
                            label: 'Pendiente de Revisión',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        },{
                            element: Checkbox,
                            props: {
                            id:'pedientes',
                            checked: true,
                            name:'pedientes',
                            label: 'Listo para Enviar',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        },
                        {
                            element: Checkbox,
                            props: {
                            id:'pedientes',
                            checked: true,
                            name:'pedientes',
                            label: 'Esperando Autorización',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        },{
                            element: Checkbox,
                            props: {
                            id:'pedientes',
                            checked: true,
                            name:'pedientes',
                            label: 'Listos Para Facturar',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        },{
                            element: Checkbox,
                            props: {
                            id:'pedientes',
                            checked: true,
                            name:'pedientes',
                            label: 'Facturados',
                            value: 'pendientes',
                            classname: 'rounded-lg border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500',
                            }
                        }
                        ]"

                />
</template>