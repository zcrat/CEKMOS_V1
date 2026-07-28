<script setup lang="ts">
interface IconAction {
    icon?: string;
    text?: string;
    title: string;
    onClick: () => void;
    classname?: string;
}

defineProps<{
    actions: IconAction[];
}>();
</script>

<template>
    <div
        v-if="actions.length > 0"
        class="grid grid-cols-2 place-items-center gap-1"
    >
        <button
            v-for="action in actions"
            :key="action.title"
            type="button"
            :title="action.title"
            :aria-label="action.title"
            :class="[
                'flex h-8 w-8 items-center justify-center rounded-md border shadow-sm transition-colors',
                action.classname
                    ?? 'border-slate-300 bg-white text-slate-600 hover:border-slate-400 hover:bg-slate-100 hover:text-slate-900',
            ]"
            @click="action.onClick"
        >
            <font-awesome-icon v-if="action.icon" :icon="action.icon" />
            <span v-else class="text-xs font-bold">{{ action.text }}</span>
        </button>
    </div>
</template>
