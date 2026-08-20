<script setup lang="ts">
import datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue'
import ZDPopover from '@/components/Zcrat/Elements/ZDPopover.vue'
</script>
<template>
    
    <ZDPopover :father="'fechas'" :classname="''"
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
