<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    items: { estado: string; total: number; colorClass: string; dotClass: string }[];
}>();

const total = computed(() => props.items.reduce((sum, i) => sum + i.total, 0));

function widthPercent(value: number) {
    return total.value === 0 ? 0 : (value / total.value) * 100;
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <div v-if="total > 0" class="flex h-3 w-full overflow-hidden rounded-full bg-muted">
            <div v-for="item in items" :key="item.estado" :class="item.colorClass"
                :style="{ width: `${widthPercent(item.total)}%` }" />
        </div>
        <p v-else class="text-sm text-muted-foreground">Sin datos aún.</p>

        <div class="flex flex-col gap-2">
            <div v-for="item in items" :key="item.estado" class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full" :class="item.dotClass" />
                    <span class="capitalize text-muted-foreground">{{ item.estado }}</span>
                </div>
                <span class="font-medium">{{ item.total }}</span>
            </div>
        </div>
    </div>
</template>