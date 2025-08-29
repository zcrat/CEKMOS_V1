<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: 'auto',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-white'],
    },
});

let open = ref(false);
let isnoti =computed(() => {

    if (props.align === 'noti-right' || props.align === 'noti-left-up') {
        return 'bg-black sm:bg-none opacity-10 sm:opacity-0';
    }
     return '';
});
const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        '48': 'w-48',
        'auto':'w-auto'
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left-up') {
        return 'absolute ltr:origin-top-left rtl:origin-top-right start-[100%] top-[100%]  transform  translate-x-[0] -translate-y-[100%] sm:ms-4';
    }
    if (props.align === 'noti-left-up') {
        return 'fixed right-2 top-[4.5rem] sm:right-auto sm:top-auto sm:absolute sm:ltr:origin-top-left sm:rtl:origin-top-right sm:start-[100%] sm:top-[100%]  transform  sm:translate-x-[0] sm:-translate-y-[100%] sm:ms-6';
    }

    if (props.align === 'left') {
        return 'absolute ltr:origin-top-left rtl:origin-top-right start-0 mt-2';
    }
    if (props.align === 'center') {
        return 'absolute ltr:origin-top-left rtl:origin-top-right start-[50%] transform -translate-x-[50%] ';
    }

    if (props.align === 'right') {
        return 'absolute ltr:origin-top-right rtl:origin-top-left end-0';
    }
    if (props.align === 'noti-right') {
        return 'fixed end-2 top-[4.5rem] sm:top-[100%] sm:absolute sm:ltr:origin-top-right sm:rtl:origin-top-left sm:end-0';
    }

    return 'origin-top';
});
</script>

<template>
    <div :class="['relative w-fit']">
        <div @click="open = ! open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" :class="['fixed inset-0 z-40', isnoti]" @click="open = false" />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class=" z-50 rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses]"
                style="display: none;"
                @click="open = false"
            >
                <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>
