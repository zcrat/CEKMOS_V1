<script setup lang="ts">
const props = defineProps<{
width:number;
}>();
let porcentaje:string = '0%';
if (props.width > 100) {
  porcentaje = '100%';
} else if (props.width < 0) {
  porcentaje = '0%';
} else {
  porcentaje = props.width+ '%';
}
</script>
<template>
    <div className="barra">
        <div :class="['w-[{{porcentaje}}]']"><p>{{porcentaje}}</p></div>
    </div>
</template> 