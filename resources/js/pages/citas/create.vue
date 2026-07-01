<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as citasIndex,
    store as storeCita,
} from '@/actions/App/Http/Controllers/CitaController';

const props = defineProps<{
    pacientes: { id: number; name: string; lastName: string }[];
    medicos: { id: number; name: string; lastName: string; especialidad: string | null }[];
    servicios: { id: number; titulo: string; duracion: string; precio: number }[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Citas', href: citasIndex() },
            { title: 'Agendar cita', href: '' },
        ],
    },
});

const horaInicio = ref('');
const selectedServicioId = ref<string>('');

const selectedServicio = computed(() =>
    props.servicios.find((s) => String(s.id) === selectedServicioId.value) ?? null
);
</script>

<template>
    <Head title="Agendar Cita" />

    <div class="flex flex-row justify-center m-6">
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Agendar Cita</CardTitle>
            </CardHeader>
            <CardContent>
                <Form v-bind="storeCita.form()" v-slot="{ errors, processing }"
                    class="flex flex-col gap-6">
                    <div class="grid gap-6">

                        <div class="grid gap-2">
                            <Label for="paciente_id">Paciente</Label>
                            <select id="paciente_id" name="paciente_id" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="" disabled selected>Selecciona un paciente</option>
                                <option v-for="p in pacientes" :key="p.id" :value="p.id">
                                    {{ p.name }} {{ p.lastName }}
                                </option>
                            </select>
                            <InputError :message="errors.paciente_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="medico_id">Médico</Label>
                            <select id="medico_id" name="medico_id" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="" disabled selected>Selecciona un médico</option>
                                <option v-for="m in medicos" :key="m.id" :value="m.id">
                                    {{ m.name }} {{ m.lastName }} — {{ m.especialidad ?? 'Sin especialidad' }}
                                </option>
                            </select>
                            <InputError :message="errors.medico_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="servicio_id">Servicio</Label>
                            <select id="servicio_id" name="servicio_id" required v-model="selectedServicioId"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="" disabled selected>Selecciona un servicio</option>
                                <option v-for="s in servicios" :key="s.id" :value="s.id">
                                    {{ s.titulo }}
                                </option>
                            </select>
                            <p v-if="selectedServicio" class="text-xs text-muted-foreground">
                                Duración: {{ selectedServicio.duracion }} · Precio: {{ selectedServicio.precio.toFixed(2) }} Bs.
                            </p>
                            <InputError :message="errors.servicio_id" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="hora_inicio">Inicio</Label>
                                <input id="hora_inicio" name="hora_inicio" type="datetime-local" required
                                    v-model="horaInicio"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                                <InputError :message="errors.hora_inicio" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="hora_fin">Fin</Label>
                                <input id="hora_fin" name="hora_fin" type="datetime-local" required
                                    :min="horaInicio"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                                <InputError :message="errors.hora_fin" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="estado">Estado</Label>
                            <select id="estado" name="estado" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="aprobado" selected>Aprobado</option>
                                <option value="pospuesto">Pospuesto</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                            <InputError :message="errors.estado" />
                        </div>

                        <Button type="submit" class="mt-2 w-full" :disabled="processing">
                            <Spinner v-if="processing" />
                            Agendar cita
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>