<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Pill } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Card, CardContent } from '@/components/ui/card';
import { index as reportesIndex } from '@/actions/App/Http/Controllers/ReporteController';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Reportes', href: reportesIndex() },
            { title: 'Historial de tratamientos', href: '' },
        ],
    },
});
</script>

<template>
    <Head title="Historial de tratamientos" />

    <div class="m-6 flex flex-col gap-6">
        <Heading
            title="Historial de tratamientos"
            description="Genera un reporte en PDF con los tratamientos aplicados a los pacientes."
        />

        <Card>
            <CardContent class="flex flex-col items-center gap-3 py-12 text-center text-muted-foreground">
                <Pill class="size-10" />
                <p class="text-sm">Este reporte está en construcción. Muy pronto podrás generarlo desde aquí.</p>
            </CardContent>
        </Card>
    </div>
</template>
