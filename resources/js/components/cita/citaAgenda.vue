<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Trash2 } from '@lucide/vue';
import {
    updateEstado as updateEstadoCita,
    destroy as destroyCita,
} from '@/actions/App/Http/Controllers/CitaController';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

interface CitaItem {
    id: number;
    estado: string;
    hora_inicio: string;
    hora_fin: string;
    paciente: { id: number; name: string; lastName: string };
    medico: { id: number; name: string; lastName: string; especialidad: string | null };
    servicio: { id: number; titulo: string; duracion: string; precio: number };
}

const props = defineProps<{ citas: CitaItem[] }>();

/* ---------- filtering ---------- */

type FilterValue = 'proximas' | 'hoy' | 'pasadas' | 'todas';

const filterOptions: { value: FilterValue; label: string }[] = [
    { value: 'proximas', label: 'Próximas' },
    { value: 'hoy', label: 'Hoy' },
    { value: 'pasadas', label: 'Pasadas' },
    { value: 'todas', label: 'Todas' },
];

const filter = ref<FilterValue>('proximas');

function isSameDay(a: Date, b: Date) {
    return a.getFullYear() === b.getFullYear()
        && a.getMonth() === b.getMonth()
        && a.getDate() === b.getDate();
}

const filteredCitas = computed(() => {
    const now = new Date();
    return props.citas.filter((c) => {
        const start = new Date(c.hora_inicio);
        if (filter.value === 'hoy') return isSameDay(start, now);
        if (filter.value === 'proximas') return start >= now;
        if (filter.value === 'pasadas') return start < now;
        return true;
    });
});

/* ---------- grouping by local day ---------- */

const groupedByDate = computed(() => {
    const groups: Record<string, { date: Date; citas: CitaItem[] }> = {};

    for (const c of filteredCitas.value) {
        const start = new Date(c.hora_inicio);
        const key = `${start.getFullYear()}-${start.getMonth()}-${start.getDate()}`;
        if (!groups[key]) groups[key] = { date: start, citas: [] };
        groups[key].citas.push(c);
    }

    return Object.values(groups)
        .sort((a, b) => a.date.getTime() - b.date.getTime())
        .map((g) => ({
            ...g,
            citas: g.citas.slice().sort(
                (a, b) => new Date(a.hora_inicio).getTime() - new Date(b.hora_inicio).getTime()
            ),
        }));
});

/* ---------- formatting ---------- */

function formatDayLabel(date: Date) {
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setDate(now.getDate() + 1);

    if (isSameDay(date, now)) return 'Hoy';
    if (isSameDay(date, tomorrow)) return 'Mañana';

    const label = new Intl.DateTimeFormat('es-BO', {
        weekday: 'long', day: 'numeric', month: 'long',
    }).format(date);
    return label.charAt(0).toUpperCase() + label.slice(1);
}

function formatTime(dateStr: string) {
    return new Intl.DateTimeFormat('es-BO', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateStr));
}

function estadoBadgeClass(estado: string) {
    switch (estado) {
        case 'aprobado':
            return 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
        case 'pospuesto':
            return 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300';
        case 'cancelado':
            return 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-muted text-muted-foreground';
    }
}

/* ---------- actions ---------- */

function setEstado(cita: CitaItem, estado: string) {
    router.patch(updateEstadoCita({ cita: cita.id }), { estado });
}

const pendingAction = ref<{ type: 'cancel' | 'delete'; cita: CitaItem } | null>(null);

function requestCancel(cita: CitaItem) {
    pendingAction.value = { type: 'cancel', cita };
}
function requestDelete(cita: CitaItem) {
    pendingAction.value = { type: 'delete', cita };
}
function cancelPending() {
    pendingAction.value = null;
}
function confirmPending() {
    if (!pendingAction.value) return;
    const { type, cita } = pendingAction.value;

    if (type === 'cancel') {
        router.patch(updateEstadoCita({ cita: cita.id }), { estado: 'cancelado' }, {
            onFinish: () => { pendingAction.value = null; },
        });
    } else {
        router.delete(destroyCita({ cita: cita.id }), {
            onFinish: () => { pendingAction.value = null; },
        });
    }
}
</script>

<template>
    <div class="flex flex-col gap-4">

        <div class="flex flex-wrap gap-2">
            <Button v-for="opt in filterOptions" :key="opt.value" type="button" size="sm"
                :variant="filter === opt.value ? 'default' : 'outline'" @click="filter = opt.value">
                {{ opt.label }}
            </Button>
        </div>
    </div>
    <div class="mt-4 flex flex-col gap-4 items-center">
        <p v-if="groupedByDate.length === 0" class="py-12 text-center text-muted-foreground">
            No hay citas para mostrar.
        </p>

        <Card v-for="group in groupedByDate" :key="group.date.toISOString()" class="w-full lg:3/4 xl:w-1/2">
            <CardHeader class="pb-2">
                <CardTitle class="text-base">{{ formatDayLabel(group.date) }}</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col divide-y divide-border p-0">
                <div v-for="cita in group.citas" :key="cita.id"
                    class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:gap-4">

                    <!-- time -->
                    <div class="flex items-center gap-2 sm:w-24 sm:flex-col sm:items-start sm:gap-0">
                        <span class="text-sm font-semibold">{{ formatTime(cita.hora_inicio) }}</span>
                        <span class="text-xs text-muted-foreground">– {{ formatTime(cita.hora_fin) }}</span>
                    </div>

                    <div class="hidden h-10 w-px bg-border sm:block" />

                    <!-- info -->
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="truncate font-medium">
                                {{ cita.paciente.name }} {{ cita.paciente.lastName }}
                            </p>
                            <span :class="estadoBadgeClass(cita.estado)"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                {{ cita.estado }}
                            </span>
                        </div>
                        <p class="truncate text-sm text-muted-foreground">
                            Dr(a). {{ cita.medico.name }} {{ cita.medico.lastName }}
                            <span v-if="cita.medico.especialidad"> · {{ cita.medico.especialidad }}</span>
                        </p>
                        <p class="truncate text-sm text-muted-foreground">
                            {{ cita.servicio.titulo }} · {{ cita.servicio.duracion }}
                        </p>
                    </div>

                    <!-- actions -->
                    <div class="flex flex-wrap gap-2 sm:justify-end">
                        <template v-if="puede('Cita.editar')">
                            <Button v-if="cita.estado !== 'aprobado'" type="button" size="sm" variant="outline"
                                @click="setEstado(cita, 'aprobado')">
                                Aprobar
                            </Button>
                            <Button v-if="cita.estado !== 'pospuesto'" type="button" size="sm" variant="outline"
                                @click="setEstado(cita, 'pospuesto')">
                                Posponer
                            </Button>
                            <Button v-if="cita.estado !== 'cancelado'" type="button" size="sm" variant="outline"
                                class="text-destructive hover:bg-destructive/10" @click="requestCancel(cita)">
                                Cancelar
                            </Button>
                        </template>
                        <Button v-if="puede('Cita.eliminar')" type="button" size="icon" variant="ghost" class="hover:bg-destructive/10"
                            @click="requestDelete(cita)">
                            <Trash2 class="h-4 w-4 text-destructive" />
                            <span class="sr-only">Eliminar cita</span>
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>



    <Dialog :open="pendingAction !== null" @update:open="cancelPending">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{ pendingAction?.type === 'delete' ? '¿Eliminar cita?' : '¿Cancelar cita?' }}
                </DialogTitle>
                <DialogDescription>
                    <template v-if="pendingAction?.type === 'delete'">
                        Esta acción eliminará permanentemente el registro de la cita. No se puede deshacer.
                    </template>
                    <template v-else>
                        La cita se marcará como cancelada. Podrás revertir esto más tarde marcándola como aprobada
                        nuevamente.
                    </template>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" @click="cancelPending">Volver</Button>
                <Button variant="destructive" @click="confirmPending">
                    {{ pendingAction?.type === 'delete' ? 'Sí, eliminar' : 'Sí, cancelar' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>