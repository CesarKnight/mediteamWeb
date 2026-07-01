<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import {
    store as storeDiagnostico,
    update as updateDiagnostico,
} from '@/actions/App/Http/Controllers/DiagnosticoController';

const props = defineProps<{
    historiaId: number;
    diagnostico?: {
        id: number;
        diagnostico: string;
        enfermedad: string;
        gravedad: string;
    } | null;
}>();

const emit = defineEmits<{
    cancel: [];
    saved: [];
}>();

const isEditing = !!props.diagnostico;

const formProps = isEditing
    ? updateDiagnostico.form({ historia: props.historiaId, diagnostico: props.diagnostico!.id })
    : storeDiagnostico.form({ historia: props.historiaId });
</script>

<template>
    <Form v-bind="formProps" v-slot="{ errors, processing }" @success="emit('saved')"
        class="flex flex-col gap-4 rounded-md border border-input p-4">

        <div class="grid gap-2">
            <Label for="diagnostico">Diagnóstico</Label>
            <textarea id="diagnostico" name="diagnostico" required :defaultValue="diagnostico?.diagnostico"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
            <InputError :message="errors.diagnostico" />
        </div>

        <div class="grid gap-2">
            <Label for="enfermedad">Enfermedad</Label>
            <textarea id="enfermedad" name="enfermedad" required :defaultValue="diagnostico?.enfermedad"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
            <InputError :message="errors.enfermedad" />
        </div>

        <div class="grid gap-2">
            <Label for="gravedad">Gravedad</Label>
            <select id="gravedad" name="gravedad" required
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                <option value="leve" :selected="diagnostico?.gravedad === 'leve'">Leve</option>
                <option value="medio" :selected="diagnostico?.gravedad === 'medio'">Medio</option>
                <option value="severo" :selected="diagnostico?.gravedad === 'severo'">Severo</option>
            </select>
            <InputError :message="errors.gravedad" />
        </div>

        <div class="flex gap-2">
            <Button type="submit" :disabled="processing">
                <Spinner v-if="processing" />
                {{ isEditing ? 'Guardar cambios' : 'Crear diagnóstico' }}
            </Button>
            <Button type="button" variant="outline" @click="emit('cancel')">
                Cancelar
            </Button>
        </div>
    </Form>
</template>