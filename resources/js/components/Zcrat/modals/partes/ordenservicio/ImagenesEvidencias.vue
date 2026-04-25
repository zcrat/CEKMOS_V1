<script setup lang="ts">
import Subtitle from '@/components/Zcrat/Elements/Subtitle.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import { FilesForm } from '@/types/OrdenServicio';
import { CovertBlobToURL, DeleteImage, DeleteImagesNew, SaveImagesEvidencia } from '@/utils/functions/ordenservicio';
import { ref } from 'vue';

const Imagenes = defineModel<FilesForm[]>('Imagenes', { default: [] })
const LoadImages = ref<HTMLInputElement | null>(null)
const imagepreview=ref<string>('')
defineProps<{CanEditImages:boolean}>()
</script>
<template>
    <div class="pb-2 border-2 p-2 rounded  mt-2">
      <Subtitle>Evidencias</Subtitle>
      <div class="flex flex-col sm:flex-row gap-2 w-full">
          <input v-if="CanEditImages" type="file" name="LoadImages" id="LoadImages" ref="LoadImages" multiple style="position: absolute; left: -9999px;" accept="image/*" 
          @change="(event)=>{SaveImagesEvidencia({event,Imagenes})}"/>
          <div class="flex flex-row sm:flex-col gap-2 w-full sm:w-[8rem]" >
          <Button text="Tomar Fotos"  @click="()=>{LoadImages?.click()}" />
          <Button text="Eliminar Fotos" @click="()=>{DeleteImagesNew(Imagenes)}"  type="delete" v-if="Imagenes.some(img => img.tipo_id === 3)"/>
        </div>
        <div :class="'overflow-x-auto flex gap-2 flex-row'">
          <div :key="index" v-for="(value,index) in Imagenes.filter(item=>item.tipo_id ===3)" class="border-2 rounded-md border-gray-700 p-2 w-fit flex flex-col justify-end">
             <img :src="CovertBlobToURL(value.file)" class="max-w-[200px] max-h-[200px]" @click="imagepreview=CovertBlobToURL(value.file)" />
            <Button text="Eliminar"  type="delete" @click="DeleteImage({index,Imagenes})" v-if="CanEditImages"/>
          </div>
        </div>
      </div>
      <div v-if="imagepreview != ''" class='fixed inset-0 bg-black/50 flex items-center justify-center z-50'  @click="imagepreview=''">
        <img :src="imagepreview" alt="imagen de evidencia" class="max-w-[90vw] max-h-[90vh] rounded-md shadow-lg"/>
      </div>
    </div>
</template>