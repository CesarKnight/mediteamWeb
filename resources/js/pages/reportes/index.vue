<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Activity,
    CalendarClock,
    FileText,
    Landmark,
    Pill,
    Stethoscope,
    Wallet,
} from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import {
    historialCitas,
    historialMedico,
    historialPagosPaciente,
    historialPaciente,
    historialTratamientos,
    index as reportesIndex,
} from '@/actions/App/Http/Controllers/ReporteController';
import type { RouteDefinition } from '@/wayfinder';

type Reporte = {
    title: string;
    description: string;
    icon: typeof FileText;
    href: RouteDefinition<'get'>;
};

type Categoria = {
    title: string;
    icon: typeof FileText;
    reportes: Reporte[];
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Reportes', href: reportesIndex() }],
    },
});

const categorias: Categoria[] = [
    {
        title: 'Atención Médica',
        icon: Stethoscope,
        reportes: [
            {
                title: 'Historial de Paciente',
                description: 'Consultas, diagnósticos y tratamientos registrados para un paciente.',
                icon: FileText,
                href: historialPaciente(),
            },
            {
                title: 'Historial de tratamientos',
                description: 'Tratamientos aplicados a los pacientes en un periodo determinado.',
                icon: Pill,
                href: historialTratamientos(),
            },
        ],
    },
    {
        title: 'Desempeño Médico',
        icon: Activity,
        reportes: [
            {
                title: 'Historial de médico',
                description: 'Actividad y desempeño de un médico dentro de la clínica.',
                icon: Stethoscope,
                href: historialMedico(),
            },
            {
                title: 'Historial de Citas',
                description: 'Citas atendidas, pospuestas y canceladas por médico o periodo.',
                icon: CalendarClock,
                href: historialCitas(),
            },
        ],
    },
    {
        title: 'Administración',
        icon: Landmark,
        reportes: [
            {
                title: 'Historial de Pagos Paciente',
                description: 'Pagos realizados por un paciente y su estado de cobro.',
                icon: Wallet,
                href: historialPagosPaciente(),
            },
        ],
    },
];
</script>

<template>
    <Head title="Reportes" />
    <h1 class="sr-only">Reportes</h1>

    <div class="m-6 flex flex-col gap-8">
        <Heading
            title="Reportes"
            description="Genera reportes en PDF a partir de la información registrada en el sistema."
        />

        <div v-for="categoria in categorias" :key="categoria.title" class="space-y-3">
            <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                <component :is="categoria.icon" class="size-4" />
                {{ categoria.title }}
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <Link
                    v-for="reporte in categoria.reportes"
                    :key="reporte.title"
                    :href="reporte.href"
                    class="flex items-start gap-3 rounded-lg border p-4 text-left transition-colors hover:bg-accent"
                >
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                        <component :is="reporte.icon" class="size-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium">{{ reporte.title }}</p>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            {{ reporte.description }}
                        </p>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
