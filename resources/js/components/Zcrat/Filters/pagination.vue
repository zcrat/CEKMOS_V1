<script setup lang="ts">
    import { ref, computed, watch, toRefs} from 'vue';
    import { paginationprops } from '../../../types/generales';

    const props = defineProps<paginationprops>();
    const pages=ref<number>(8);

    const { currentPage, totalPages, totalItems } = toRefs(props)
    const itemsPerPage=ref<number>(props.itemsPerPage);

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

    watch(itemsPerPage, (value) => {
        props.onSearch(1, value);
    });
    const setPage=(currentPage:number)=>{
            props.onSearch(currentPage,itemsPerPage.value);
    }

</script>
<template>
    <div :class="'flex gap-2 flex-wrap items-center w-full my-2 '+(props.clases ? props.clases : '')">
        <select v-if="props.chnagesItems && totalItems>1"
            name="ElementsPerPage"
            id="ElementsPerPage"
            v-model="itemsPerPage"
            class="py-0 pl-1 rounded-md border border-[var(--color1)] max-h-8 w-full min-h-8 sm:w-auto"
            >
            <option
                v-for="value in Math.max(1, Math.floor(Math.min(totalItems, 200) / 5))"
                :key="value"
                :label="((value + 1) * 5) + ''"
                :value="(value + 1) * 5"
            />
        </select>
        <div v-if="totalPages>1" class='flex justify-between max-[640px]:w-full gap-[0.075rem] items-center'>
            <button  class='PageInactive' :disabled="currentPage === 1 ? true : false" @click="()=>setPage(currentPage-1)"><font-awesome-icon icon="fa-solid fa-left-long "/></button>
            <button  :class="currentPage === 1 ? 'PageActive':'PageInactive'" @click="()=>setPage(1)">1</button>
            <button
                v-for="num in visiblePages"
                :key="'currentPage-' + num"
                :class="currentPage === num ? 'PageActive' : 'PageInactive'"
                @click="setPage(num)"
                >
                {{ num }}
            </button>

            <button  :class="currentPage === totalPages ?'PageActive':'PageInactive'" @click="()=>setPage(totalPages)">{{totalPages}}</button>
            <button  :disabled="currentPage === totalPages" class='PageInactive' @click="()=>setPage(totalPages)"><font-awesome-icon icon="fa-solid fa-right-long "/></button>
        </div> 
      </div> 
</template>