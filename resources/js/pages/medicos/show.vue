<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Pencil } from '@lucide/vue';
import MedicoCard from '@/components/medico/medicoCard.vue';
import {
    index as medicosIndex,
    edit as editMedico,
} from '@/actions/App/Http/Controllers/MedicoController';

defineProps<{
    medico: {
        id: number;
        especialidad: string | null;
    };
    user: {
        id: number;
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
            { title: 'Médicos', href: medicosIndex() },
            { title: 'Ver Médico', href: '' },
        ],
    },
});
</script>

<template>
    <Head :title="`${user.name} ${user.lastName}`" />

    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">
                    MEDICO
                </h2>
                <p class="text-sm text-muted-foreground">Ficha del médico</p>
            </div>
            <Button as-child>
                <Link :href="editMedico({ medico: medico.id })">
                    <Pencil class="mr-2 h-4 w-4" />
                    Editar
                </Link>
            </Button>
        </div>

        <MedicoCard :medico="medico" :user="user" />

        <!-- Future relationships go here -->
    </div>
</template>