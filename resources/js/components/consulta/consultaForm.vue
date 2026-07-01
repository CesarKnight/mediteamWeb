<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import {
    store as storeConsulta,
    update as updateConsulta,
} from '@/actions/App/Http/Controllers/ConsultaController';

const props = defineProps<{
    historiaId: number;
    consulta?: {
        id: number;
        motivo: string;
        peso: number;
        altura: number;
    } | null;
}>();

const emit = defineEmits<{
    cancel: [];
    saved: [];
}>();

const isEditing = !!props.consulta;

const formProps = isEditing
    ? updateConsulta.form({ historia: props.historiaId, consulta: props.consulta!.id })
    : storeConsulta.form({ historia: props.historiaId });
</script>

<template>
    <Form v-bind="formProps" v-slot="{ errors, processing }" @success="emit('saved')"
        class="flex flex-col gap-4 rounded-md border border-input p-4">

        <div class="grid gap-2">
            <Label for="motivo">Motivo</Label>
            <textarea id="motivo" name="motivo" required :defaultValue="consulta?.motivo"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
            <InputError :message="errors.motivo" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
                <Label for="peso">Peso (kg)</Label>
                <input id="peso" name="peso" type="number" step="0.01" required :defaultValue="consulta?.peso"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                <InputError :message="errors.peso" />
            </div>

            <div class="grid gap-2">
                <Label for="altura">Altura (m)</Label>
                <input id="altura" name="altura" type="number" step="0.01" required :defaultValue="consulta?.altura"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                <InputError :message="errors.altura" />
            </div>
        </div>

        <div class="flex gap-2">
            <Button type="submit" :disabled="processing">
                <Spinner v-if="processing" />
                {{ isEditing ? 'Guardar cambios' : 'Crear consulta' }}
            </Button>
            <Button type="button" variant="outline" @click="emit('cancel')">
                Cancelar
            </Button>
        </div>
    </Form>
</template>