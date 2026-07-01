<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Spinner } from '@/components/ui/spinner';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as pagosIndex,
    store as storePago,
} from '@/actions/App/Http/Controllers/PagoController';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pagos', href: pagosIndex() },
            { title: 'Registrar pago', href: '' },
        ],
    },
});
const props = defineProps<{
    pacientes: { id: number; name: string; lastName: string }[];
    servicios: { id: number; titulo: string; precio: number }[];
}>();

const selectedServicioId = ref<number | ''>('');
const total = ref<string>('');
const totalEditable = ref(false);
const metodo = ref('efectivo');
const estado = ref('pendiente');

const selectedServicio = computed(() =>
    props.servicios.find((s) => s.id === selectedServicioId.value) ?? null
);

const estadoLocked = computed(() => metodo.value === 'QR');

// cambiar total al de servicio
watch(selectedServicioId, () => {
    if (selectedServicio.value && !totalEditable.value) {
        total.value = selectedServicio.value.precio.toFixed(2);
    }
});

watch(totalEditable, (editable) => {
    if (!editable && selectedServicio.value) {
        total.value = selectedServicio.value.precio.toFixed(2);
    }
});

// cuando cambia a qr se bloquea estado
watch(metodo, (val) => {
    if (val === 'QR') {
        estado.value = 'pendiente';
    }
});
</script>

<template>

    <Head title="Registrar Pago" />

    <div class="flex flex-row justify-center m-6">
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Registrar Pago</CardTitle>
            </CardHeader>
            <CardContent>
                <Form v-bind="storePago.form()" v-slot="{ errors, processing }" class="flex flex-col gap-6">
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
                            <Label for="servicio_id">Servicio</Label>
                            <select id="servicio_id" name="servicio_id" required v-model="selectedServicioId"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="" disabled selected>Selecciona un servicio</option>
                                <option v-for="s in servicios" :key="s.id" :value="s.id">
                                    {{ s.titulo }} — {{ s.precio.toFixed(2) }} Bs.
                                </option>
                            </select>
                            <InputError :message="errors.servicio_id" />
                        </div>

                        <div class="grid gap-2">
                            <div class="flex items-center justify-between">
                                <Label for="total">Total (Bs.)</Label>
                                <div class="flex items-center gap-2">
                                    <Label for="total-editable" class="text-xs font-normal text-muted-foreground">
                                        Editar manualmente
                                    </Label>
                                    <Switch v-model="totalEditable" id="total-editable" />
                                </div>
                            </div>
                            <input id="total" name="total" type="number" step="0.01" required v-model="total"
                                :readonly="!totalEditable"
                                :class="!totalEditable ? 'cursor-not-allowed opacity-50' : ''"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring" />
                            <p class="text-xs text-muted-foreground">
                                Se autocompleta con el precio del servicio. Activa "Editar manualmente" para ajustarlo.
                            </p>
                            <InputError :message="errors.total" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="metodo">Método</Label>
                                <select id="metodo" name="metodo" required v-model="metodo"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="QR">QR</option>
                                </select>
                                <InputError :message="errors.metodo" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="estado">Estado</Label>
                                <select id="estado" name="estado" required v-model="estado" :disabled="estadoLocked"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="pagado">Pagado</option>
                                    <option value="anulado">Anulado</option>
                                </select>
                                <!-- disabled selects don't submit; this carries the value through when locked -->
                                <input v-if="estadoLocked" type="hidden" name="estado" value="pendiente" />
                                <p v-if="estadoLocked" class="text-xs text-muted-foreground">
                                    Los pagos QR quedan pendientes hasta que el procesador de pagos confirme la
                                    transacción.
                                </p>
                                <InputError :message="errors.estado" />
                            </div>
                        </div>

                        <Button type="submit" class="mt-2 w-full" :disabled="processing">
                            <Spinner v-if="processing" />
                            Registrar pago
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>