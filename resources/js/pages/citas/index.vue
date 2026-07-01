<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    create as createCita,
    index as citasIndex,
} from '@/actions/App/Http/Controllers/CitaController';
import CitaAgenda from '@/components/cita/citaAgenda.vue';
import { CalendarPlus } from '@lucide/vue';
import Button from '@/components/ui/button/Button.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Citas', href: citasIndex() }],
    },
});

defineProps<{
    citas: {
        id: number;
        estado: string;
        hora_inicio: string;
        hora_fin: string;
        paciente: { id: number; name: string; lastName: string };
        medico: { id: number; name: string; lastName: string; especialidad: string | null };
        servicio: { id: number; titulo: string; duracion: string; precio: number };
    }[];
}>();
</script>

<template>
    <Head title="Citas" />
    <h1 class="sr-only">Citas</h1>
    <div class="m-6 flex flex-col gap-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Citas</h2>
                <p class="text-sm text-muted-foreground">
                    {{ citas.length }} cita{{ citas.length !== 1 ? 's' : '' }} registrada{{ citas.length !== 1 ? 's' : '' }}
                </p>
            </div>
            <Button as-child>
                <Link :href="createCita()">
                    <CalendarPlus class="mr-2 h-4 w-4" />
                    Agendar cita
                </Link>
            </Button>
        </div>
        <CitaAgenda :citas="citas" />
    </div>
</template>