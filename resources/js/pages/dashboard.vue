<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    DollarSign, Users, ClipboardList, Clock, Stethoscope, ArrowRight,
    Calendar,
} from '@lucide/vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import { Button } from '@/components/ui/button';
import KpiCard from '@/components/dashboard/kpiCard.vue';
import RevenueChart from '@/components/dashboard/revenueChart.vue';
import EstadoBreakdown from '@/components/dashboard/estadoBreakdown.vue';
import CitaAgendaList from '@/components/dashboard/citaAgendaList.vue';
import ServiciosTopList from '@/components/dashboard/serviciosTopList.vue';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import { index as citasIndex } from '@/actions/App/Http/Controllers/CitaController';
import PagosPendientesList from '@/components/dashboard/pagosPendientesList.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Panel', href: dashboardIndex() }],
    },
});

const props = defineProps<{
    kpis: {
        citasHoy: number;
        ingresosMes: number;
        pacientesActivos: number;
        historiasPendientes: number;
        pagosPendientes: number;
        medicosTotal: number;
    };
    citasHoy: { id: number; estado: string; hora_inicio: string; paciente: string; medico: string; servicio: string }[];
    proximasCitas: { id: number; estado: string; hora_inicio: string; paciente: string; medico: string; servicio: string }[];
    ingresosPorMes: { mes: string; total: number }[];
    citasPorEstado: { estado: string; total: number }[];
    historiasPorEstado: { estado: string; total: number }[];
    serviciosTop: { id: number; titulo: string; ingresos: number }[];
    pagosPendientes: { id: number; total: number; metodo: string; paciente: string; servicio: string }[];
}>();

const page = usePage();
const userName = (page.props.auth as { user?: { name?: string } } | undefined)?.user?.name;

const citasColorMap: Record<string, { colorClass: string; dotClass: string }> = {
    aprobado:  { colorClass: 'bg-green-500', dotClass: 'bg-green-500' },
    pospuesto: { colorClass: 'bg-amber-500', dotClass: 'bg-amber-500' },
    cancelado: { colorClass: 'bg-red-500', dotClass: 'bg-red-500' },
};

const historiasColorMap: Record<string, { colorClass: string; dotClass: string }> = {
    pendiente:  { colorClass: 'bg-amber-500', dotClass: 'bg-amber-500' },
    anulado:    { colorClass: 'bg-red-500', dotClass: 'bg-red-500' },
    finalizado: { colorClass: 'bg-green-500', dotClass: 'bg-green-500' },
};

const citasPorEstadoItems = props.citasPorEstado.map((c) => ({ ...c, ...citasColorMap[c.estado] }));
const historiasPorEstadoItems = props.historiasPorEstado.map((h) => ({ ...h, ...historiasColorMap[h.estado] }));
</script>

<template>
    <Head title="Panel" />

    <div class="m-6 flex flex-col gap-6">

        <div>
            <h2 class="text-2xl font-semibold tracking-tight">
                {{ userName ? `Hola, ${userName}` : 'Panel' }}
            </h2>
            <p class="text-sm text-muted-foreground">Resumen general de la clínica.</p>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
            <KpiCard :icon="Calendar" label="Citas hoy" :value="String(kpis.citasHoy)"
                accent-class="bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300" />
            <KpiCard :icon="DollarSign" label="Ingresos del mes" :value="`${kpis.ingresosMes.toFixed(2)} Bs.`"
                accent-class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300" />
            <KpiCard :icon="Users" label="Pacientes activos" :value="String(kpis.pacientesActivos)"
                accent-class="bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300" />
            <KpiCard :icon="ClipboardList" label="Historias pendientes" :value="String(kpis.historiasPendientes)"
                accent-class="bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300" />
            <KpiCard :icon="Clock" label="Pagos pendientes" :value="String(kpis.pagosPendientes)"
                accent-class="bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300" />
            <KpiCard :icon="Stethoscope" label="Médicos" :value="String(kpis.medicosTotal)"
                accent-class="bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300" />
        </div>

        <!-- Ingresos + Citas por estado -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle>Ingresos mensuales</CardTitle>
                    <CardDescription>Pagos confirmados en los últimos 6 meses.</CardDescription>
                </CardHeader>
                <CardContent>
                    <RevenueChart :data="ingresosPorMes" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Citas por estado</CardTitle>
                    <CardDescription>Distribución histórica.</CardDescription>
                </CardHeader>
                <CardContent>
                    <EstadoBreakdown :items="citasPorEstadoItems" />
                </CardContent>
            </Card>
        </div>

        <!-- Agenda -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Agenda de hoy</CardTitle>
                        <CardDescription>Citas programadas para hoy.</CardDescription>
                    </div>
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="citasIndex()">
                            Ver todas <ArrowRight class="ml-1 h-4 w-4" />
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <CitaAgendaList :citas="citasHoy" empty-message="No hay citas programadas para hoy." />
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Próximas citas</CardTitle>
                        <CardDescription>Lo siguiente en la agenda.</CardDescription>
                    </div>
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="citasIndex()">
                            Ver todas <ArrowRight class="ml-1 h-4 w-4" />
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <CitaAgendaList :citas="proximasCitas" empty-message="No hay próximas citas agendadas." />
                </CardContent>
            </Card>
        </div>

        <!-- Servicios, Historias, Pagos -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <Card>
                <CardHeader>
                    <CardTitle>Servicios más rentables</CardTitle>
                    <CardDescription>Por ingresos confirmados.</CardDescription>
                </CardHeader>
                <CardContent>
                    <ServiciosTopList :servicios="serviciosTop" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Historias clínicas</CardTitle>
                    <CardDescription>Distribución por estado.</CardDescription>
                </CardHeader>
                <CardContent>
                    <EstadoBreakdown :items="historiasPorEstadoItems" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Pagos pendientes</CardTitle>
                    <CardDescription>Los más recientes.</CardDescription>
                </CardHeader>
                <CardContent>
                    <PagosPendientesList :pagos="pagosPendientes" />
                </CardContent>
            </Card>
        </div>

    </div>
</template>