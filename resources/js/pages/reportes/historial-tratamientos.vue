<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { FileText, Pill, ListChecks, Download } from '@lucide/vue';
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
    generarHistorialTratamientos,
    index as reportesIndex,
} from '@/actions/App/Http/Controllers/ReporteController';

type PacienteOption = {
    id: number;
    name: string;
    lastName: string;
    ci: string;
};

type Filtros = {
    paciente_id: number;
    fecha_inicio: string | null;
    fecha_fin: string | null;
};

type Resumen = {
    paciente: string;
    totalTratamientos: number;
    medicamentosUnicos: number;
};

const props = defineProps<{
    pacientes: PacienteOption[];
    filtros?: Filtros | null;
    pdfBase64?: string | null;
    resumen?: Resumen | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Reportes', href: reportesIndex() },
            { title: 'Historial de tratamientos', href: '' },
        ],
    },
});

const pacienteId = ref(props.filtros?.paciente_id ? String(props.filtros.paciente_id) : '');
const fechaInicio = ref(props.filtros?.fecha_inicio ?? '');
const fechaFin = ref(props.filtros?.fecha_fin ?? '');

const pdfDataUrl = computed(() =>
    props.pdfBase64 ? `data:application/pdf;base64,${props.pdfBase64}` : null,
);
</script>

<template>
    <Head title="Historial de tratamientos" />

    <div class="m-6 flex flex-col gap-6">
        <Heading
            title="Historial de tratamientos"
            description="Genera un reporte en PDF con la cronología de tratamientos aplicados a un paciente."
        />

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <FileText class="size-4" />
                    Parámetros del reporte
                </CardTitle>
                <CardDescription>
                    Selecciona el paciente y, opcionalmente, un rango de fechas para acotar los tratamientos.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="generarHistorialTratamientos.form()"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-2 sm:col-span-1">
                            <Label for="paciente_id">Paciente</Label>
                            <select
                                id="paciente_id"
                                v-model="pacienteId"
                                name="paciente_id"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="" disabled>Selecciona un paciente</option>
                                <option v-for="p in pacientes" :key="p.id" :value="p.id">
                                    {{ p.name }} {{ p.lastName }} — CI {{ p.ci }}
                                </option>
                            </select>
                            <InputError :message="errors.paciente_id" />
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
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-2">
                <Card>
                    <CardContent class="flex items-center gap-3 py-4">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <Pill class="size-5" />
                        </div>
                        <div>
                            <p class="text-lg font-semibold leading-none">{{ resumen.totalTratamientos }}</p>
                            <p class="text-xs text-muted-foreground">Tratamientos</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-3 py-4">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <ListChecks class="size-5" />
                        </div>
                        <div>
                            <p class="text-lg font-semibold leading-none">{{ resumen.medicamentosUnicos }}</p>
                            <p class="text-xs text-muted-foreground">Medicamentos distintos</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Vista previa</CardTitle>
                        <CardDescription>{{ resumen.paciente }}</CardDescription>
                    </div>
                    <Button as-child variant="outline" size="sm">
                        <a :href="pdfDataUrl" download="historial-tratamientos.pdf">
                            <Download class="mr-2 size-4" />
                            Descargar PDF
                        </a>
                    </Button>
                </CardHeader>
                <CardContent>
                    <iframe
                        :src="pdfDataUrl"
                        class="h-[800px] w-full rounded-md border"
                        title="Vista previa del historial de tratamientos"
                    />
                </CardContent>
            </Card>
        </template>
    </div>
</template>
