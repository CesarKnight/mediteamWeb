<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import {
    store as storeTratamiento,
    update as updateTratamiento,
} from '@/actions/App/Http/Controllers/TratamientoController';

const props = defineProps<{
    historiaId: number;
    tratamiento?: {
        id: number;
        medicamento: string;
        frecuencia_horas: number;
    } | null;
}>();

const emit = defineEmits<{
    cancel: [];
    saved: [];
}>();

const isEditing = !!props.tratamiento;

const formProps = isEditing
    ? updateTratamiento.form({ historia: props.historiaId, tratamiento: props.tratamiento!.id })
    : storeTratamiento.form({ historia: props.historiaId });
</script>

<template>
    <Form v-bind="formProps" v-slot="{ errors, processing }" @success="emit('saved')"
        class="flex flex-col gap-4 rounded-md border border-input p-4">

        <div class="grid gap-2">
            <Label for="medicamento">Medicamento</Label>
            <textarea id="medicamento" name="medicamento" required :defaultValue="tratamiento?.medicamento"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
            <InputError :message="errors.medicamento" />
        </div>

        <div class="grid gap-2">
            <Label for="frecuencia_horas">Frecuencia (horas)</Label>
            <input id="frecuencia_horas" name="frecuencia_horas" type="number" step="0.5" required
                :defaultValue="tratamiento?.frecuencia_horas"
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
            <InputError :message="errors.frecuencia_horas" />
        </div>

        <div class="flex gap-2">
            <Button type="submit" :disabled="processing">
                <Spinner v-if="processing" />
                {{ isEditing ? 'Guardar cambios' : 'Crear tratamiento' }}
            </Button>
            <Button type="button" variant="outline" @click="emit('cancel')">
                Cancelar
            </Button>
        </div>
    </Form>
</template>