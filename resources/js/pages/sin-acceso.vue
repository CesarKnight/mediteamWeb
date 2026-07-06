<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ShieldAlert } from '@lucide/vue';
import Heading from '@/components/Heading.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Sin acceso', href: '' }],
    },
});
</script>

<template>
    <Head title="Sin acceso" />

    <div class="m-6 flex flex-col items-center gap-4 py-16 text-center">
        <ShieldAlert class="h-12 w-12 text-muted-foreground" />
        <Heading
            title="No tienes acceso a ningún módulo"
            description="Tu rol no tiene permisos asignados todavía. Contacta a un administrador para que te habilite acceso."
        />
    </div>
</template>
