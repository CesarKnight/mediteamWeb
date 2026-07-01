<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Checkbox } from '@/components/ui/checkbox';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import { ref } from 'vue';
import {
    index as historiasIndex,
    update as updateHistoria,
} from '@/actions/App/Http/Controllers/HistoriaController';
import ConsultaForm from '@/components/consulta/consultaForm.vue';
import DiagnosticoForm from '@/components/diagnostico/diagnosticoForm.vue';
import TratamientoForm from '@/components/tratamiento/tratamientoForm.vue';
import { Plus } from '@lucide/vue';

const props = defineProps<{
    historia: {
        id: number;
        estado: string;
        paciente: { id: number; name: string; lastName: string };
        medicoCreador: { id: number; name: string; lastName: string; especialidad: string | null };
        medicosInvolucrados: { id: number; name: string; lastName: string }[];
        consultas: { id: number; motivo: string; peso: number; altura: number; created_at: string }[];
        diagnosticos: { id: number; diagnostico: string; enfermedad: string; gravedad: string; created_at: string }[];
        tratamientos: { id: number; medicamento: string; frecuencia_horas: number; created_at: string }[];
    };
    pacientes: { id: number; name: string; lastName: string }[];
    medicos: { id: number; name: string; lastName: string; especialidad: string | null }[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Historias Clínicas', href: historiasIndex() },
            { title: `Ver historia`, href: '' },
        ],
    },
});

// Pre-select the medicos involucrados already attached to this historia
const selectedInvolucrados = ref<number[]>(
    props.historia.medicosInvolucrados.map(m => m.id)
);

function toggleInvolucrado(id: number, checked: boolean) {
    if (checked) {
        selectedInvolucrados.value.push(id);
    } else {
        selectedInvolucrados.value = selectedInvolucrados.value.filter(m => m !== id);
    }
}

// vars para mostrar consultas
const showCreateConsulta = ref(false);
const editingConsultaId = ref<number | null>(null);

function openCreateConsulta() {
    editingConsultaId.value = null;
    showCreateConsulta.value = true;
}
function openEditConsulta(id: number) {
    showCreateConsulta.value = false;
    editingConsultaId.value = id;
}
function closeConsultaForms() {
    showCreateConsulta.value = false;
    editingConsultaId.value = null;
}

//funciones para diagnosticos

const showCreateDiagnostico = ref(false);
const editingDiagnosticoId = ref<number | null>(null);

function openCreateDiagnostico() {
    editingDiagnosticoId.value = null;
    showCreateDiagnostico.value = true;
}
function openEditDiagnostico(id: number) {
    showCreateDiagnostico.value = false;
    editingDiagnosticoId.value = id;
}
function closeDiagnosticoForms() {
    showCreateDiagnostico.value = false;
    editingDiagnosticoId.value = null;
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('es-BO', {
        day: '2-digit', month: '2-digit', year: 'numeric',
    });
}

//funciones de tratamientos


const showCreateTratamiento = ref(false);
const editingTratamientoId = ref<number | null>(null);

function openCreateTratamiento() {
    editingTratamientoId.value = null;
    showCreateTratamiento.value = true;
}
function openEditTratamiento(id: number) {
    showCreateTratamiento.value = false;
    editingTratamientoId.value = id;
}
function closeTratamientoForms() {
    showCreateTratamiento.value = false;
    editingTratamientoId.value = null;
}
</script>

<template>

    <Head :title="`Historia #${historia.id}`" />

    <div class="flex flex-col items-center m-6">
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Historia Clínica #{{ historia.id }}</CardTitle>
                <CardDescription>
                    Paciente: {{ historia.paciente.name }} {{ historia.paciente.lastName }}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form v-bind="updateHistoria.form({ historia: historia.id })" v-slot="{ errors, processing }"
                    class="flex flex-col gap-6">
                    <div class="grid gap-6">

                        <div class="grid gap-2">
                            <Label for="paciente_id">Paciente</Label>
                            <select id="paciente_id" name="paciente_id" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option v-for="p in pacientes" :key="p.id" :value="p.id"
                                    :selected="p.id === historia.paciente.id">
                                    {{ p.name }} {{ p.lastName }}
                                </option>
                            </select>
                            <InputError :message="errors.paciente_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="medico_id">Médico creador</Label>
                            <select id="medico_id" name="medico_id" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option v-for="m in medicos" :key="m.id" :value="m.id"
                                    :selected="m.id === historia.medicoCreador.id">
                                    {{ m.name }} {{ m.lastName }} — {{ m.especialidad ?? 'Sin especialidad' }}
                                </option>
                            </select>
                            <InputError :message="errors.medico_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="estado">Estado</Label>
                            <select id="estado" name="estado" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="pendiente" :selected="historia.estado === 'pendiente'">Pendiente</option>
                                <option value="anulado" :selected="historia.estado === 'anulado'">Anulado</option>
                                <option value="finalizado" :selected="historia.estado === 'finalizado'">Finalizado
                                </option>
                            </select>
                            <InputError :message="errors.estado" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Médicos involucrados</Label>
                            <div class="flex flex-col gap-2 rounded-md border border-input p-3">
                                <p v-if="medicos.length === 0" class="text-sm text-muted-foreground">
                                    No hay médicos disponibles.
                                </p>
                                <label v-for="m in medicos" :key="m.id" class="flex items-center gap-2 text-sm">
                                    <Checkbox :model-value="selectedInvolucrados.includes(m.id)"
                                        @update:model-value="(val) => toggleInvolucrado(m.id, val as boolean)" />
                                    {{ m.name }} {{ m.lastName }}
                                </label>
                            </div>
                            <input v-for="id in selectedInvolucrados" :key="id" type="hidden"
                                name="medicos_involucrados[]" :value="id" />
                            <InputError :message="errors.medicos_involucrados" />
                        </div>

                        <Button type="submit" class="mt-2 w-full" :disabled="processing">
                            <Spinner v-if="processing" />
                            Guardar cambios
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>

        <!-- Carta de consultas -->
        <Card class="w-full lg:w-1/2 mt-6">
            <CardHeader>
                <CardTitle>Consultas</CardTitle>
                <CardDescription>Registros de consultas asociadas a esta historia.</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">

                <p v-if="historia.consultas.length === 0 && !showCreateConsulta" class="text-sm text-muted-foreground">
                    No hay consultas registradas.
                </p>

                <template v-for="c in historia.consultas" :key="c.id">
                    <ConsultaForm v-if="editingConsultaId === c.id" :historia-id="historia.id" :consulta="c"
                        @cancel="closeConsultaForms" @saved="closeConsultaForms" />
                    <div v-else class="flex items-center justify-between rounded-md border border-input p-3 text-sm">
                        <div>
                            <p class="font-medium">{{ c.motivo }}</p>
                            <p class="text-muted-foreground">Peso: {{ c.peso }} kg · Altura: {{ c.altura }} m</p>
                            <p class="text-xs text-muted-foreground mt-1">
                                Registrado: {{ formatDate(c.created_at) }}
                            </p>
                        </div>
                        <Button variant="outline" size="sm" @click="openEditConsulta(c.id)">Editar</Button>
                    </div>
                </template>

                <ConsultaForm v-if="showCreateConsulta" :historia-id="historia.id" @cancel="closeConsultaForms"
                    @saved="closeConsultaForms" />

                <div class="flex flex-wrap gap-2 border-t border-input pt-4">
                    <Button v-if="!showCreateConsulta" @click="openCreateConsulta">
                        <Plus/>  Agregar consulta
                    </Button>
                </div>
            </CardContent>
        </Card>


        <!-- carta de diagnosticos -->
        <Card class="w-full lg:w-1/2 mt-6">
            <CardHeader>
                <CardTitle>Diagnósticos</CardTitle>
                <CardDescription>Diagnósticos asociados a esta historia.</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">

                <p v-if="historia.diagnosticos.length === 0 && !showCreateDiagnostico"
                    class="text-sm text-muted-foreground">
                    No hay diagnósticos registrados.
                </p>

                <template v-for="d in historia.diagnosticos" :key="d.id">
                    <DiagnosticoForm v-if="editingDiagnosticoId === d.id" :historia-id="historia.id" :diagnostico="d"
                        @cancel="closeDiagnosticoForms" @saved="closeDiagnosticoForms" />
                    <div v-else class="flex items-center justify-between rounded-md border border-input p-3 text-sm">
                        <div>
                            <p class="font-medium">{{ d.diagnostico }}</p>
                            <p class="text-muted-foreground">
                                {{ d.enfermedad }} · Gravedad: {{ d.gravedad }}
                            </p>
                            <p class="text-xs text-muted-foreground mt-1">
                                Registrado: {{ formatDate(d.created_at) }}
                            </p>
                        </div>
                        <Button type="button" variant="outline" size="sm" @click="openEditDiagnostico(d.id)">
                            Editar
                        </Button>
                    </div>
                </template>

                <DiagnosticoForm v-if="showCreateDiagnostico" :historia-id="historia.id" @cancel="closeDiagnosticoForms"
                    @saved="closeDiagnosticoForms" />

                <div class="flex flex-wrap gap-2 border-t border-input pt-4">
                    <Button type="button" v-if="!showCreateDiagnostico" @click="openCreateDiagnostico">
                        <Plus/>  Agregar diagnóstico
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- carta de tratamientos -->
        <Card class="w-full lg:w-1/2 mt-6">
            <CardHeader>
                <CardTitle>Tratamientos</CardTitle>
                <CardDescription>Tratamientos asociados a esta historia.</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">

                <p v-if="historia.tratamientos.length === 0 && !showCreateTratamiento"
                    class="text-sm text-muted-foreground">
                    No hay tratamientos registrados.
                </p>

                <template v-for="t in historia.tratamientos" :key="t.id">
                    <TratamientoForm v-if="editingTratamientoId === t.id" :historia-id="historia.id" :tratamiento="t"
                        @cancel="closeTratamientoForms" @saved="closeTratamientoForms" />
                    <div v-else class="flex items-center justify-between rounded-md border border-input p-3 text-sm">
                        <div>
                            <p class="font-medium">{{ t.medicamento }}</p>
                            <p class="text-muted-foreground">Cada {{ t.frecuencia_horas }} horas</p>
                            <p class="text-xs text-muted-foreground mt-1">
                                Registrado: {{ formatDate(t.created_at) }}
                            </p>
                        </div>
                        <Button type="button" variant="outline" size="sm" @click="openEditTratamiento(t.id)">
                            Editar
                        </Button>
                    </div>
                </template>

                <TratamientoForm v-if="showCreateTratamiento" :historia-id="historia.id" @cancel="closeTratamientoForms"
                    @saved="closeTratamientoForms" />

                <div class="flex flex-wrap gap-2 border-t border-input pt-4">
                    <Button type="button" v-if="!showCreateTratamiento" @click="openCreateTratamiento">
                        <Plus/> Agregar tratamiento
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>