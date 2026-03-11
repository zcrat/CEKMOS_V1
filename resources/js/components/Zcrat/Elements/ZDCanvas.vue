<script setup lang="ts">
import { ref, onMounted } from "vue"
import Button from "../Inputs/Button.vue";
const props=withDefaults(defineProps<{
  width?:number
  height?:number
}>(),{
  width:400,
  height:200
})
const canvasRef = ref<HTMLCanvasElement | null>(null)
let ctx: CanvasRenderingContext2D | null = null
let drawing = false

onMounted(() => {
  const canvas = canvasRef.value
  if (!canvas) return

  ctx = canvas.getContext("2d")

  canvas.addEventListener("mousedown", () => drawing = true)
  window.addEventListener("mouseup", () => {
    drawing = false
    ctx?.beginPath()
  })
  canvas.addEventListener("mousemove", draw)
})

function draw(e: MouseEvent) {
  if (!drawing || !ctx) return

  ctx.lineWidth = 2
  ctx.lineCap = "round"

  ctx.lineTo(e.offsetX, e.offsetY)
  ctx.stroke()

  ctx.beginPath()
  ctx.moveTo(e.offsetX, e.offsetY)
}

function clearCanvas() {
  const canvas = canvasRef.value
  if (!ctx || !canvas) return

  ctx.clearRect(0, 0, canvas.width, canvas.height)
}
</script>

<template>
  <div>
    <canvas
      ref="canvasRef"
      :height="height+'px'"
      class="border-4 rounded border-[--micolor] w-full"
    ></canvas>

    <div class="flex w-fit gap-2">
      <Button text="Limpiar" @click="clearCanvas" type="save"/>
      <button @click="clearCanvas" class="">Limpiar</button>
      <button @click="clearCanvas">Limpiar</button>
    </div>
  </div>
</template>