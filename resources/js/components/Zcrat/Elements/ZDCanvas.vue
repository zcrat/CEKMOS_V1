<script setup lang="ts">
import { ref, onMounted } from "vue"
import Button from "../Inputs/Button.vue";

interface Point{
  x:number
  y:number
}

const props=withDefaults(defineProps<{
  classnamedivcanvas?:string
  disabled?:boolean
}>(),{
  disabled:false,
  classnamedivcanvas:'w-full h-[25rem]'
})

const canvasRef = ref<HTMLCanvasElement | null>(null)
let ctx: CanvasRenderingContext2D | null = null
let drawing = false

const Strokes=ref<Point[][]>([])
const currentStroke= ref<Point[]>([])

onMounted(() => {
  const canvas = canvasRef.value
  if (!canvas) return

  ctx = canvas.getContext("2d")
  const rect = canvas.getBoundingClientRect()

  canvas.width = rect.width
  canvas.height = rect.height

  canvas.addEventListener("mousedown", (e) => {
    drawing = true
    currentStroke.value = [{
      x: e.offsetX,
      y: e.offsetY
    }]
  })
  window.addEventListener("mouseup", () => {
    if (drawing && currentStroke.value.length>0) {
        Strokes.value.push(currentStroke.value)
    }
    drawing = false
    ctx?.beginPath()
  })
  canvas.addEventListener("mousemove", draw)
})
function dibujarImagen(urlimagen:string) {
  const canvas = canvasRef.value
   if (!canvas) return
  const img = new Image();
  img.src = urlimagen;
  img.onload = function () {
    const ctx = canvas.getContext("2d")
    const imgWidth = img.width;
    const imgHeight = img.height;
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;
    const imgAspectRatio = imgWidth / imgHeight;
    const canvasAspectRatio = canvasWidth / canvasHeight;
    let renderWidth, renderHeight;
    if (imgAspectRatio > canvasAspectRatio) {
      renderWidth = canvasWidth;
      renderHeight = canvasWidth / imgAspectRatio;
    } else {
      renderHeight = canvasHeight;
      renderWidth = canvasHeight * imgAspectRatio;
    }
    ctx?.clearRect(0, 0, canvas.width, canvas.height);
    ctx?.drawImage(img, 0, 0, renderWidth, renderHeight);
  };
}
function draw(e: MouseEvent) {
  if (!drawing || !ctx || props.disabled) return

  ctx.lineWidth = 2
  ctx.lineCap = "round"

  currentStroke.value.push({
    x: e.offsetX,
    y: e.offsetY
  })

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
function redraw(){
  const canvas = canvasRef.value
  if(!canvas || !ctx) return

  ctx.clearRect(0,0,canvas.width,canvas.height)

  Strokes.value.forEach(stroke => {
    ctx?.beginPath()

    stroke.forEach((point, i) => {
      if(i === 0){
        ctx?.moveTo(point.x, point.y)
      } else {
        ctx?.lineTo(point.x, point.y)
      }
    })

    ctx?.stroke()
  })
}
function undo(){
  Strokes.value.pop()
  redraw()
}
defineExpose({
  dibujarImagen
})
</script>

<template>
  <div>
    <div :class="classnamedivcanvas">
      <canvas
        ref="canvasRef"
        class="border-4 rounded border-[--micolor] w-full h-full"
      ></canvas>
    </div>

    <div class="flex w-fit gap-2">
      <Button text="Limpiar" @click="clearCanvas" type="delete"/>
      <Button text="Deshacer" @click="undo" type="secondary"/>
    </div>
  </div>
</template>