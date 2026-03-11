<script setup lang="ts">
import { ref, onMounted } from "vue"
import SignaturePad from "signature_pad"

const canvasRef = ref<HTMLCanvasElement | null>(null)
let signaturePad: SignaturePad

onMounted(() => {
  if (canvasRef.value) {
    signaturePad = new SignaturePad(canvasRef.value)
  }
})

function clear(){
  signaturePad.clear()
}

function save(){
  const data = signaturePad.toDataURL()
  console.log(data)
}
</script>

<template>
  <div>
    <canvas ref="canvasRef" width="400" height="200" class="border-2 border-black"></canvas>
    <button @click="save">Guardar</button>
    <button @click="clear">Limpiar</button>
  </div>

</template>