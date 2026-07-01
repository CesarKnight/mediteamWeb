<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    data: { mes: string; total: number }[];
}>();

const max = computed(() => Math.max(...props.data.map((d) => d.total), 1));

function heightPercent(total: number) {
    return Math.max((total / max.value) * 100, 2); // 2% floor so zero-months are still visible
}
</script>

<template>
    <div class="flex h-48 items-end justify-between gap-2">
        <div v-for="d in data" :key="d.mes" class="flex flex-1 flex-col items-center gap-2">
            <div class="flex h-full w-full items-end">
                <div class="w-full rounded-t-md bg-primary/80 transition-all"
                    :style="{ height: `${heightPercent(d.total)}%` }"
                    :title="`${d.total.toFixed(2)} Bs.`" />
            </div>
            <span class="text-xs capitalize text-muted-foreground">{{ d.mes }}</span>
        </div>
    </div>
</template>