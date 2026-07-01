<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { edit as editServicio } from '@/actions/App/Http/Controllers/ServicioController';

const props = defineProps<{
    servicios: { id: number; titulo: string; ingresos: number }[];
}>();

const max = computed(() => Math.max(...props.servicios.map((s) => s.ingresos), 1));

function widthPercent(value: number) {
    return Math.max((value / max.value) * 100, 3);
}
</script>

<template>
    <div class="flex flex-col gap-3">
        <p v-if="servicios.length === 0" class="py-8 text-center text-sm text-muted-foreground">
            Aún no hay ingresos registrados por servicio.
        </p>

        <div v-for="s in servicios" :key="s.id" class="cursor-pointer"
            @click="() => router.visit(editServicio({ servicio: s.id }))">
            <div class="mb-1 flex items-center justify-between text-sm">
                <span class="truncate font-medium">{{ s.titulo }}</span>
                <span class="shrink-0 text-muted-foreground">{{ s.ingresos.toFixed(2) }} Bs.</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                <div class="h-full rounded-full bg-primary/80" :style="{ width: `${widthPercent(s.ingresos)}%` }" />
            </div>
        </div>
    </div>
</template>