<script setup lang="ts">
import { ref, onMounted } from "vue"
import Button from "../Inputs/Button.vue";
import axios from 'axios' 
import MyBasicToast from "@/utils/ToastNotificationBasic";
interface Point{
  x:number
  y:number
}

const props=withDefaults(defineProps<{
  classnamedivcanvas?:string
  disabled?:boolean
  title?:string
  strokecolor?:'red'|'black'
}>(),{
  disabled:false,
  strokecolor:'black',
  classnamedivcanvas:'w-full h-[20rem]'
})

const canvasRef = ref<HTMLCanvasElement | null>(null)
let ctx: CanvasRenderingContext2D | null = null
let drawing = false

const Strokes=ref<Point[][]>([])
const StrokesDelete=ref<Point[][]>([])
const ImageDraw=ref<Blob|null>(null)
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
        StrokesDelete.value
    }
    drawing = false
    ctx?.beginPath()
  })
  canvas.addEventListener("mousemove", draw)
})

  
const dibujarImagen= async (blob:Blob|null) =>{
  ImageDraw.value = blob
  redraw()
}
const DrawImage = async () => {
  const canvas = canvasRef.value
  if (!canvas || !ImageDraw.value) return
  const ctx = canvas.getContext("2d")
  if (!ctx) return
  const url = URL.createObjectURL(ImageDraw.value)
  const img = new Image()
  await new Promise<void>((resolve, reject) => {
    img.onload = () => {
      const imgWidth = img.width
      const imgHeight = img.height
      const canvasWidth = canvas.width
      const canvasHeight = canvas.height
      const imgAspectRatio = imgWidth / imgHeight
      const canvasAspectRatio = canvasWidth / canvasHeight
      let renderWidth, renderHeight
      if (imgAspectRatio > canvasAspectRatio) {
        renderWidth = canvasWidth
        renderHeight = canvasWidth / imgAspectRatio
      } else {
        renderHeight = canvasHeight
        renderWidth = canvasHeight * imgAspectRatio
      }
      ctx.clearRect(0, 0, canvas.width, canvas.height)
      ctx.drawImage(img, 0, 0, renderWidth, renderHeight)
      URL.revokeObjectURL(url)
      resolve()
    }

    img.onerror = reject
    img.src = url
  })
}
function draw(e: MouseEvent) {
  if (!drawing || !ctx || props.disabled) return

  ctx.lineWidth = 2
  ctx.strokeStyle = props.strokecolor
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
async function getCanvasBlob(): Promise<Blob | null> {
  const canvas = canvasRef.value
  if (!canvas) return null

  return new Promise(resolve => {
    canvas.toBlob(blob => {
      resolve(blob)
    }, "image/png")
  })
}

function clearCanvas() {
  StrokesDelete.value=Strokes.value
  Strokes.value=[]
  redraw()
}
async function redraw(){
  const canvas = canvasRef.value
  if(!canvas || !ctx) return

  ctx.clearRect(0,0,canvas.width,canvas.height)

  await DrawImage()

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
  if (Strokes.value.length > 0) {
    const removed = Strokes.value.pop();   // quita el último
    if (removed) {
      StrokesDelete.value.push(removed);   // lo guarda en el historial
    }
    redraw();
  }

}
function redo(){
  if (StrokesDelete.value.length > 0) {
    const removed = StrokesDelete.value.pop();   // quita el último
    if (removed) {
      Strokes.value.push(removed);   // lo guarda en el historial
    }
    redraw();
  }
}
defineExpose({
  dibujarImagen,
  getCanvasBlob
})
</script>

<template>
  <div>
    <h2 class="w-full text-center text-3xl font-semibold capitalize" v-if="title">{{ title }}</h2>
    <div :class="classnamedivcanvas">
      <canvas
        ref="canvasRef"
        class="border-4 rounded border-[--micolor] w-full h-full"
      ></canvas>
    </div>

    <div class="flex w-fit gap-2">
      <Button text="Limpiar" :disabled="Strokes.length <= 0" @click="clearCanvas" type="delete"/>
      <Button icon="fa-solid fa-angle-left" :disabled="Strokes.length <= 0" @click="undo" type="secondary"/>
      <Button icon="fa-solid fa-angle-right" :disabled="StrokesDelete.length <= 0" @click="redo" type="secondary"/>
    </div>
  </div>
</template>