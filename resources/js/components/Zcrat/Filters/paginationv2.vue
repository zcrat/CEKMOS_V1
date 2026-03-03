<script setup lang="ts">
    import { ref, computed, watch, toRefs} from 'vue';

    interface PaginationProps {
        totalElements: number
        loading?: boolean
        changesItems?: boolean
        clases?:string
    }
    export interface paginationrefs
    {
        currentPage:number
        itemsPerPage:number
        totalElements:number
    }
    const props = withDefaults(defineProps<PaginationProps>(), {
        loading: false,
        changesItems: true
    })

    const { totalElements, loading, changesItems } = toRefs(props)
    const currentPage = defineModel<number>('currentPage', { default: 1 })
    const itemsPerPage = defineModel<number>('itemsPerPage', { default: 10 })
    const pages=ref<number>(8);

    const totalPages = computed(() => {
        const items = Number(totalElements.value) || 0
        const perPage = Number(itemsPerPage.value) || 1 // evita división por 0
        return Math.max(1, Math.ceil(items / perPage))
    })
    const visiblePages = computed(() => {
        const visible: number[] = [];
        for (let i = 2; i < totalPages.value; i++) {
            const isStart = currentPage.value <= (pages.value / 2 + 1) && ((i - 1) <= pages.value);
            const isEnd = currentPage.value >= totalPages.value - (pages.value / 2) && i >= totalPages.value - pages.value;
            const isMiddle = i >= currentPage.value - pages.value / 2 && i < currentPage.value + pages.value / 2;
            if (isStart || isEnd || isMiddle) {
            visible.push(i);
            }
        }
        return visible;
    });
    const setPage=(val:number)=>{
        currentPage.value=val;
    }

</script>
<template>
    <div :class="'flex gap-2 flex-wrap items-center w-full my-2 '+(props.clases ? props.clases : '')">
        <select v-if="changesItems && totalElements>1"
            name="ElementsPerPage"
            id="ElementsPerPage"
            v-model="itemsPerPage"
            :disabled="loading"
            class="py-0 pl-1 rounded-md border border-[var(--color1)] max-h-8 w-full min-h-8 sm:w-auto"
        >
            <option
                v-for="value in Math.max(1, Math.floor(Math.min(totalElements, 200) / 5))"
                :key="value"
                :label="((value + 1) * 5) + ''"
                :value="(value + 1) * 5"
            />
        </select>
        <div v-if="totalPages>1" class='flex justify-between max-[640px]:w-full gap-[0.075rem] items-center'>
            <button  class="PageInactive" :disabled="currentPage <= 1 || loading" @click="()=>setPage(currentPage-1)"><font-awesome-icon icon="fa-solid fa-left-long "/></button>
            <button  :disabled="loading" :class="currentPage === 1 ? 'PageActive':'PageInactive'" @click="()=>setPage(1)">1</button>
            <button
                v-for="num in visiblePages"
                :key="'currentPage-' + num"
                :class="currentPage === num ? 'PageActive' : 'PageInactive'"
                @click="setPage(num)"
                :disabled="loading"
                >
                {{ num }}
            </button>

            <button  :disabled="loading" :class="currentPage === totalPages ?'PageActive':'PageInactive'" @click="()=>setPage(totalPages)">{{totalPages}}</button>
            <button  :disabled="currentPage >= totalPages || loading" class='PageInactive' @click="()=>setPage(currentPage+1)"><font-awesome-icon icon="fa-solid fa-right-long "/></button>
        </div> 
      </div> 
</template>