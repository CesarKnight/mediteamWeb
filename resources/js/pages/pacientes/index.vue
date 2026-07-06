<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { SquarePlus } from '@lucide/vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as pacientesIndex,
} from '@/actions/App/Http/Controllers/PacienteController';
import { create as createUser } from '@/actions/App/Http/Controllers/UserController';
import PacienteTable from '@/components/paciente/pacienteTable.vue';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Pacientes', href: pacientesIndex() }],
    },
});

defineProps<{
    pacientes: {
        id: number;
        estado: string;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        email: string;
    }[];
}>();


</script>

<template>
    <Head title="Pacientes" />
    <h1 class="sr-only">Pacientes</h1>

    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Pacientes</h2>
                <p class="text-sm text-muted-foreground">
                    {{ pacientes.length }} paciente{{ pacientes.length !== 1 ? 's' : '' }} registrado{{ pacientes.length !== 1 ? 's' : '' }}
                </p>
            </div>

            <Button v-if="puede('Usuario.crear')" as-child>
                <Link :href="createUser()">
                    <SquarePlus class="mr-2 h-4 w-4" />
                    Añadir Paciente
                </Link>
            </Button>
        </div>

        <Card>
            <CardContent>
                <PacienteTable :pacientes="pacientes"/>
            </CardContent>
        </Card>
    </div>
</template>