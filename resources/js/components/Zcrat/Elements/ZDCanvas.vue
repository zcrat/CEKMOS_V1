<script setup lang="ts">
import { ref, onMounted, watch } from "vue"
import Button from "../Inputs/Button.vue"
import ZDListErrors from "./ZDListErrors.vue"
import ZDIconError from "./ZDIconError.vue"

interface Point {
  x: number
  y: number
}

export type ImageDraw = Blob | null | string

export interface StrokesArray {
  Strokes: Point[][]
  StrokesDelete: Point[][]
  currentStroke: Point[]
  ImageDraw: ImageDraw
}

const props = withDefaults(defineProps<{
  classnamedivcanvas?: string
  height?:string
  disabled?: boolean
  title?: string
  image?: string
  strokecolor?: 'red' | 'black'
  errors?: string[]
  ImageRequired?: boolean
  DeleteErrors?: ()=>void
}>(), {
  disabled: false,
  strokecolor: 'black',
  classnamedivcanvas: 'w-full',
  height: 'h-[20rem]'
})

const canvasRef = ref<HTMLCanvasElement | null>(null)

let ctx: CanvasRenderingContext2D | null = null
let drawing = false

const Strokes = ref<Point[][]>([])
const StrokesDelete = ref<Point[][]>([])
const ImageDraw = ref<ImageDraw>(null)
const currentStroke = ref<Point[]>([])

const imageInfo = ref<{
  x:number
  y:number
  width:number
  height:number
} | null>(null)

watch(
  () => [Strokes.value.length, ImageDraw.value],
  () => {
    props.DeleteErrors?.();
  }
)

watch(
  () => ImageDraw.value,
  () => {
    if (props.ImageRequired) {
      clearCanvas()
    }
  }
)

onMounted(() => {
  const canvas = canvasRef.value
  if (!canvas) return

  canvas.style.touchAction = "none"

  ctx = canvas.getContext("2d")
  sizeCanvas()

  canvas.addEventListener("pointerdown", (e) => {
    if (
      props.disabled ||
      (!ImageDraw.value && props.ImageRequired) ||
      (props.ImageRequired && !isInsideDrawableArea(e))
    ) return

    drawing = true

    const { x, y } = getCoords(e)

    currentStroke.value = [{ x, y }]

    const { realx, realy } = RealCoords(x, y)

    ctx?.beginPath()
    ctx?.moveTo(realx, realy)
  })

  window.addEventListener("pointerup", () => {
    if (drawing && currentStroke.value.length > 0) {
      Strokes.value.push([...currentStroke.value])
      StrokesDelete.value = []
    }

    drawing = false
    ctx?.beginPath()
  })

  canvas.addEventListener("pointermove", draw)

  const observer = new ResizeObserver(() => {
    sizeCanvas()
    redraw()
  })

  observer.observe(canvas.parentElement!)
})

function getDrawableArea() {
  const canvas = canvasRef.value!
  const rect = canvas.getBoundingClientRect()

  if (
    !props.ImageRequired ||
    !ImageDraw.value ||
    !imageInfo.value
  ) {
    return {
      x: 0,
      y: 0,
      width: rect.width,
      height: rect.height
    }
  }

  return {
    x: imageInfo.value.x,
    y: imageInfo.value.y,
    width: imageInfo.value.width,
    height: imageInfo.value.height
  }
}

function getCoords(e: PointerEvent) {
  const rect = canvasRef.value!.getBoundingClientRect()
  const area = getDrawableArea()

  return {
    x: ((e.clientX - rect.left) - area.x) / area.width,
    y: ((e.clientY - rect.top) - area.y) / area.height
  }
}

function RealCoords(x: number, y: number) {
  const area = getDrawableArea()

  return {
    realx: area.x + (x * area.width),
    realy: area.y + (y * area.height)
  }
}

function isInsideDrawableArea(e: PointerEvent) {
  const rect = canvasRef.value!.getBoundingClientRect()
  const area = getDrawableArea()

  const x = e.clientX - rect.left
  const y = e.clientY - rect.top

  return (
    x >= area.x &&
    x <= area.x + area.width &&
    y >= area.y &&
    y <= area.y + area.height
  )
}

function draw(e: PointerEvent) {
  if (!drawing || !ctx || props.disabled ) return

  if (props.ImageRequired && !isInsideDrawableArea(e)) {
    ctx.beginPath()
    return
  }

  ctx.lineWidth = 2
  ctx.strokeStyle = props.strokecolor
  ctx.lineCap = "round"

  const { x, y } = getCoords(e)

  currentStroke.value.push({ x, y })

  const { realx, realy } = RealCoords(x, y)

  ctx.lineTo(realx, realy)
  ctx.stroke()

  ctx.beginPath()
  ctx.moveTo(realx, realy)
}

const dibujarImagen = async (data: ImageDraw) => {
  ImageDraw.value = data
  redraw()
}

const DrawImage = async () => {
  const canvas = canvasRef.value

  if (!canvas || !ImageDraw.value) return

  const ctx = canvas.getContext("2d")

  if (!ctx) return

  const url = typeof ImageDraw.value === 'string'
    ? ImageDraw.value
    : URL.createObjectURL(ImageDraw.value)

  const img = new Image()

  await new Promise<void>((resolve, reject) => {
    img.onload = () => {
      const imgWidth = img.width
      const imgHeight = img.height

      const rect = canvas.getBoundingClientRect()

      const canvasWidth = rect.width
      const canvasHeight = rect.height

      const imgAspectRatio = imgWidth / imgHeight
      const canvasAspectRatio = canvasWidth / canvasHeight

      let renderWidth
      let renderHeight

      if (imgAspectRatio > canvasAspectRatio) {
        renderWidth = canvasWidth
        renderHeight = canvasWidth / imgAspectRatio
      } else {
        renderHeight = canvasHeight
        renderWidth = canvasHeight * imgAspectRatio
      }

      const offsetX = (canvasWidth - renderWidth) / 2
      const offsetY = (canvasHeight - renderHeight) / 2

      imageInfo.value = {
        x: offsetX,
        y: offsetY,
        width: renderWidth,
        height: renderHeight
      }

      ctx.clearRect(0, 0, canvas.width, canvas.height)

      ctx.drawImage(
        img,
        offsetX,
        offsetY,
        renderWidth,
        renderHeight
      )

      if (typeof ImageDraw.value !== 'string') {
        URL.revokeObjectURL(url)
      }

      resolve()
    }

    img.onerror = reject
    img.src = url
  })
}

function sizeCanvas() {
  const canvas = canvasRef.value

  if (!canvas || !ctx) return

  const rect = canvas.getBoundingClientRect()
  const ratio = window.devicePixelRatio || 1

  canvas.width = rect.width * ratio
  canvas.height = rect.height * ratio

  ctx.resetTransform()
  ctx.scale(ratio, ratio)
}

async function redraw() {
  const canvas = canvasRef.value

  if (!canvas || !ctx) return

  ctx.clearRect(0, 0, canvas.width, canvas.height)

  await DrawImage()

  ctx.lineWidth = 2
  ctx.strokeStyle = props.strokecolor
  ctx.lineCap = "round"

  Strokes.value.forEach(stroke => {
    ctx?.beginPath()

    stroke.forEach((point, i) => {
      const { realx, realy } = RealCoords(point.x, point.y)

      if (i === 0) {
        ctx?.moveTo(realx, realy)
      } else {
        ctx?.lineTo(realx, realy)
      }
    })

    ctx?.stroke()
  })
}

function clearCanvas() {
  StrokesDelete.value = [...Strokes.value]
  Strokes.value = []

  redraw()
}

function undo() {
  if (Strokes.value.length > 0) {
    const removed = Strokes.value.pop()

    if (removed) {
      StrokesDelete.value.push(removed)
    }

    redraw()
  }
}

function redo() {
  if (StrokesDelete.value.length > 0) {
    const restored = StrokesDelete.value.pop()

    if (restored) {
      Strokes.value.push(restored)
    }

    redraw()
  }
}

async function getCanvasBlob(): Promise<Blob | null> {
  const canvas = canvasRef.value

  if (!canvas) return null

  return new Promise(resolve => {
    canvas.toBlob(blob => resolve(blob), "image/png")
  })
}

function GetStrokes(): StrokesArray {
  return {
    Strokes: Strokes.value,
    StrokesDelete: StrokesDelete.value,
    ImageDraw: ImageDraw.value,
    currentStroke: currentStroke.value,
  }
}

function SetStrokes(val: StrokesArray) {
  Strokes.value = val.Strokes
  StrokesDelete.value = val.StrokesDelete
  ImageDraw.value = val.ImageDraw
  currentStroke.value = val.currentStroke

  redraw()
}

defineExpose({
  dibujarImagen,
  getCanvasBlob,
  GetStrokes,
  SetStrokes
})
</script>

<template>
  <div>
    <h2
      v-if="title"
      class="w-full text-center text-3xl font-semibold capitalize"
    >
      <ZDIconError
        :errors="props.errors"
        hidden-absolute
      />
      {{ title }}
    </h2>

    <div :class="[classnamedivcanvas , image ? '' : height]">
      <img :src="image" alt="" v-if="image" class="bg-gray-200 border border-gray-400 p-2 rounded max-h-full">

      <canvas
      v-else
        ref="canvasRef"
        :class="[
          'border-4 rounded border-[--micolor] w-full h-full touch-none ',
          props.errors && props.errors.length > 0
            ? 'inputerror'
            : ''
        ]"
      ></canvas>
    </div>

    <div
      class="flex w-fit max-w-full gap-2 mt-2"
      v-if="!image"
    >
      <Button
        text="Limpiar"
        :disabled="Strokes.length <= 0"
        @click="clearCanvas"
        type="delete"
      />

      <Button
        icon="fa-solid fa-angle-left"
        :disabled="Strokes.length <= 0"
        @click="undo"
        type="secondary"
      />

      <Button
        icon="fa-solid fa-angle-right"
        :disabled="StrokesDelete.length <= 0"
        @click="redo"
        type="secondary"
      />

      <ZDListErrors :errors="props.errors"/>
    </div>
  </div>
</template>