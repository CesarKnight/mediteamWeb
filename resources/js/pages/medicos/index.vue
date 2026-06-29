<script setup lang="ts">
import { Head} from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as medicosIndex,
    destroy as destroyMedico,
} from '@/actions/App/Http/Controllers/MedicoController';
import MedicoTable from '@/components/medico/medicoTable.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Médicos', href: medicosIndex() }],
    },
});

defineProps<{
    medicos: {
        id: number;
        especialidad: string | null;
        user: {
            id: number;
            name: string;
            lastName: string;
            ci: string;
            fechaNacimiento: string;
            telefono: string;
            email: string;
        };
    }[];
}>();

const medicoToDelete = ref<{ id: number; name: string; lastName: string } | null>(null);

function confirmDelete(m: { id: number; user: { name: string; lastName: string } }) {
    medicoToDelete.value = { id: m.id, name: m.user.name, lastName: m.user.lastName };
}

function cancelDelete() {
    medicoToDelete.value = null;
}

function executeDelete() {
    if (!medicoToDelete.value) return;
    router.delete(destroyMedico({ medico: medicoToDelete.value.id }), {
        onFinish: () => { medicoToDelete.value = null; },
    });
}
</script>

<template>
    <Head title="Médicos" />
    <h1 class="sr-only">Médicos</h1>

    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Médicos</h2>
                <p class="text-sm text-muted-foreground">
                    {{ medicos.length }} médico{{ medicos.length !== 1 ? 's' : '' }} registrado{{ medicos.length !== 1 ? 's' : '' }}
                </p>
            </div>
        </div>

        <Card>
            <CardContent>
                <MedicoTable :medicos="medicos"/>
            </CardContent>
        </Card>
    </div>

    
</template>