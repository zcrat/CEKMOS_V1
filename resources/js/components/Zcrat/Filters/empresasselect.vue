<script setup lang="ts">
import { ref} from 'vue'
import  multiselect from 'vue-multiselect'
import axios from 'axios'
const isLoading = ref(false)
interface Option {
    value: number | string
    label: string
}
const empresa =ref<Option | null>(null)
const options = ref<Option[]>([])

const buscarEmpresas = async (search:string) => {
  isLoading.value = true;
  try {
    // Ejemplo de API, reemplaza con tu endpoint Laravel
    const response = await axios.get(route('Select2.Empresas'),{params:{search}});
    const data = response.data.options;
    options.value = data;
  } catch (e) {
    console.error("Error al buscar empresas:", e);
  } finally {
    isLoading.value = false;
  }
}
buscarEmpresas("");
</script>
<template>
    <div class="min-w-[10rem]">
        <multiselect
        v-model="empresa"
        :options="options"
        :loading="isLoading"
        :searchable="true"
        :internal-search="false"
        :close-on-select="true"
        :show-labels="false"
        label="label"
        track-by="value"
        placeholder="Buscar empresa..."
        aria-label="pick a value"
        @search-change="buscarEmpresas"
        />
    </div>
</template>