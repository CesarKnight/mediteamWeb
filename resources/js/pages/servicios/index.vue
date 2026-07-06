<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    create as createServicio,
    index as serviciosIndex,
} from '@/actions/App/Http/Controllers/ServicioController';
import ServicioTable from '@/components/servicio/servicioTable.vue';
import { PackagePlus } from '@lucide/vue';
import Button from '@/components/ui/button/Button.vue';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Servicios', href: serviciosIndex() }],
    },
});

defineProps<{
    servicios: {
        id: number;
        titulo: string;
        descripcion: string;
        precio: number;
        duracion: string;
        estado: string;
    }[];
}>();
</script>

<template>
    <Head title="Servicios" />
    <h1 class="sr-only">Servicios</h1>
    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Servicios</h2>
                <p class="text-sm text-muted-foreground">
                    {{ servicios.length }} servicio{{ servicios.length !== 1 ? 's' : '' }} registrado{{ servicios.length !== 1 ? 's' : '' }}
                </p>
            </div>
            <Button v-if="puede('Servicio.crear')" as-child>
                <Link :href="createServicio()">
                    <PackagePlus class="mr-2 h-4 w-4" />
                    Añadir Servicio
                </Link>
            </Button>
        </div>
        <ServicioTable :servicios="servicios" />
    </div>
</template>