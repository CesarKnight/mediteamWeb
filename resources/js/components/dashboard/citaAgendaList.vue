<script setup lang="ts">
interface CitaResumen {
    id: number;
    estado: string;
    hora_inicio: string;
    paciente: string;
    medico: string;
    servicio: string;
}

defineProps<{
    citas: CitaResumen[];
    emptyMessage: string;
}>();

function formatTime(dateStr: string) {
    return new Intl.DateTimeFormat('es-BO', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateStr));
}

function estadoBadgeClass(estado: string) {
    switch (estado) {
        case 'aprobado': return 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
        case 'pospuesto': return 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300';
        default: return 'bg-muted text-muted-foreground';
    }
}
</script>

<template>
    <div class="flex flex-col divide-y divide-border">
        <p v-if="citas.length === 0" class="py-8 text-center text-sm text-muted-foreground">
            {{ emptyMessage }}
        </p>

        <div v-for="cita in citas" :key="cita.id" class="flex items-center gap-3 py-3">
            <div class="w-14 shrink-0 text-sm font-semibold">{{ formatTime(cita.hora_inicio) }}</div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium">{{ cita.paciente }}</p>
                <p class="truncate text-xs text-muted-foreground">
                    Dr(a). {{ cita.medico }} · {{ cita.servicio }}
                </p>
            </div>
            <span :class="estadoBadgeClass(cita.estado)"
                class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                {{ cita.estado }}
            </span>
        </div>
    </div>
</template>