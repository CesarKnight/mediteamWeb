<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    create as createHistoria,
    index as historiasIndex,
} from '@/actions/App/Http/Controllers/HistoriaController';
import HistoriaTable from '@/components/historia/historiaTable.vue';
import { ClipboardPlus} from '@lucide/vue';
import Button from '@/components/ui/button/Button.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Historias Clínicas', href: historiasIndex() }],
    },
});

defineProps<{
    historias: {
        id: number;
        estado: string;
        paciente: {
            id: number;
            name: string;
            lastName: string;
        };
        medicoCreador: {    
            id: number;
            name: string;
            lastName: string;
            especialidad: string | null;
        };
        medicosInvolucrados: {
            id: number;
            name: string;
            lastName: string;
        }[];
    }[];
}>();
</script>

<template>
    <Head title="Historias Clínicas" />
    <h1 class="sr-only">Historias Clínicas</h1>

    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Historias Clínicas</h2>
                <p class="text-sm text-muted-foreground">
                    {{ historias.length }} historia{{ historias.length !== 1 ? 's' : '' }} registrada{{ historias.length !== 1 ? 's' : '' }}
                </p>
            </div>
            <Button as-child>
                <Link :href="createHistoria()">
                    <ClipboardPlus class="mr-2 h-4 w-4" />
                    Añadir Historia
                </Link>
            </Button>
        </div>

        <HistoriaTable :historias="historias" />
    </div>
</template>