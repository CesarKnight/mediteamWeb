<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Pencil } from '@lucide/vue';
import {
    index as pacientesIndex,
    show as showPaciente,
    edit as editPaciente,
} from '@/actions/App/Http/Controllers/PacienteController';
import PacienteCard from '@/components/paciente/pacienteCard.vue';

const props = defineProps<{
    paciente: {
        id: number;
        estado: string;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        email: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pacientes', href: pacientesIndex() },
            { title: 'Ver paciente', href: '' },
        ],
    },
});
</script>

<template>
    <Head :title="`${paciente.name} ${paciente.lastName}`" />

    <div class="m-6 flex flex-col gap-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">
                    PACIENTE
                </h2>
                <p class="text-sm text-muted-foreground">Ficha del paciente</p>
            </div>
            <Button as-child>
                <Link :href="editPaciente({ paciente: paciente.id })">
                    <Pencil class="mr-2 h-4 w-4" />
                    Editar
                </Link>
            </Button>
        </div>

        <!-- Paciente info -->
        <PacienteCard :paciente="paciente" />

        <!-- Future relationships go here -->

    </div>
</template>