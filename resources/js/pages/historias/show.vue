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

const props = defineProps<{
    historia: {
        id: number;
        estado: string;
        paciente: { id: number; name: string; lastName: string };
        medicoCreador: { id: number; name: string; lastName: string; especialidad: string | null };
        medicosInvolucrados: { id: number; name: string; lastName: string }[];
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
</script>

<template>

    <Head :title="`Historia #${historia.id}`" />

    <div class="flex flex-row justify-center m-6">
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
    </div>
</template>