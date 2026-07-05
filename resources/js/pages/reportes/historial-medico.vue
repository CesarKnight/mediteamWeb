<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { FileText, ClipboardList, Users, Stethoscope, Download } from '@lucide/vue';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    generarHistorialMedico,
    index as reportesIndex,
} from '@/actions/App/Http/Controllers/ReporteController';

type MedicoOption = {
    id: number;
    name: string;
    lastName: string;
    especialidad: string | null;
};

type Filtros = {
    medico_id: number;
    fecha_inicio: string | null;
    fecha_fin: string | null;
};

type Resumen = {
    medico: string;
    totalHistorias: number;
    pacientesUnicos: number;
    totalConsultas: number;
};

const props = defineProps<{
    medicos: MedicoOption[];
    filtros?: Filtros | null;
    pdfBase64?: string | null;
    resumen?: Resumen | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Reportes', href: reportesIndex() },
            { title: 'Historial de médico', href: '' },
        ],
    },
});

const medicoId = ref(props.filtros?.medico_id ? String(props.filtros.medico_id) : '');
const fechaInicio = ref(props.filtros?.fecha_inicio ?? '');
const fechaFin = ref(props.filtros?.fecha_fin ?? '');

const pdfDataUrl = computed(() =>
    props.pdfBase64 ? `data:application/pdf;base64,${props.pdfBase64}` : null,
);
</script>

<template>
    <Head title="Historial de médico" />

    <div class="m-6 flex flex-col gap-6">
        <Heading
            title="Historial de médico"
            description="Genera un reporte en PDF con las historias clínicas en las que un médico ha estado involucrado."
        />

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <FileText class="size-4" />
                    Parámetros del reporte
                </CardTitle>
                <CardDescription>
                    Selecciona el médico y, opcionalmente, un rango de fechas para acotar las historias.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="generarHistorialMedico.form()"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-2 sm:col-span-1">
                            <Label for="medico_id">Médico</Label>
                            <select
                                id="medico_id"
                                v-model="medicoId"
                                name="medico_id"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="" disabled>Selecciona un médico</option>
                                <option v-for="m in medicos" :key="m.id" :value="m.id">
                                    {{ m.name }} {{ m.lastName }} — {{ m.especialidad ?? 'Sin especialidad' }}
                                </option>
                            </select>
                            <InputError :message="errors.medico_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="fecha_inicio">Desde</Label>
                            <Input
                                id="fecha_inicio"
                                v-model="fechaInicio"
                                type="date"
                                name="fecha_inicio"
                            />
                            <InputError :message="errors.fecha_inicio" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="fecha_fin">Hasta</Label>
                            <Input
                                id="fecha_fin"
                                v-model="fechaFin"
                                type="date"
                                name="fecha_fin"
                            />
                            <InputError :message="errors.fecha_fin" />
                        </div>
                    </div>

                    <Button type="submit" class="w-full sm:w-fit" :disabled="processing">
                        <Spinner v-if="processing" />
                        Generar reporte
                    </Button>
                </Form>
            </CardContent>
        </Card>

        <template v-if="resumen && pdfDataUrl">
            <div class="grid grid-cols-3 gap-4">
                <Card>
                    <CardContent class="flex items-center gap-3 py-4">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <ClipboardList class="size-5" />
                        </div>
                        <div>
                            <p class="text-lg font-semibold leading-none">{{ resumen.totalHistorias }}</p>
                            <p class="text-xs text-muted-foreground">Historias</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-3 py-4">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <Users class="size-5" />
                        </div>
                        <div>
                            <p class="text-lg font-semibold leading-none">{{ resumen.pacientesUnicos }}</p>
                            <p class="text-xs text-muted-foreground">Pacientes distintos</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-3 py-4">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <Stethoscope class="size-5" />
                        </div>
                        <div>
                            <p class="text-lg font-semibold leading-none">{{ resumen.totalConsultas }}</p>
                            <p class="text-xs text-muted-foreground">Consultas</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Vista previa</CardTitle>
                        <CardDescription>{{ resumen.medico }}</CardDescription>
                    </div>
                    <Button as-child variant="outline" size="sm">
                        <a :href="pdfDataUrl" download="historial-medico.pdf">
                            <Download class="mr-2 size-4" />
                            Descargar PDF
                        </a>
                    </Button>
                </CardHeader>
                <CardContent>
                    <iframe
                        :src="pdfDataUrl"
                        class="h-[800px] w-full rounded-md border"
                        title="Vista previa del historial de médico"
                    />
                </CardContent>
            </Card>
        </template>
    </div>
</template>
