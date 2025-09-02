<script setup lang="ts">
import datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue'
import Poper from '@/components/Zcrat/Elements/poper.vue'
</script>
<template>
    
    <poper :father="'fechas'" :classname="''"
                    :stoppropagation="true"
                    :align="'left'"
                    :children="[
                        {
                            element: datapicker,
                            props: {
                            label: 'Rango de fechas',
                            }
                        }
                        ]"

                />
</template>