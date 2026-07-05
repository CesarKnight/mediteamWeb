<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Table, TableBody, TableCell,
    TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import { index as bitacoraIndex } from '@/actions/App/Http/Controllers/BitacoraController';

type BitacoraEntry = {
    id: number;
    descripcion: string;
    controlador: string;
    ip: string;
    created_at: string;
    user: { name: string; lastName: string } | null;
};

const props = defineProps<{
    bitacoras: BitacoraEntry[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Bitacora', href: bitacoraIndex() }],
    },
});
</script>

<template>
    <Head title="Bitacora" />
    <h1 class="sr-only">Bitacora</h1>

    <div class="m-6 flex flex-col gap-6">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Bitacora</h2>
            <p class="text-sm text-muted-foreground">
                {{ props.bitacoras.length }} registro{{ props.bitacoras.length !== 1 ? 's' : '' }} de actividad
            </p>
        </div>

        <Card>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/50">
                            <TableHead class="font-semibold">Fecha</TableHead>
                            <TableHead class="font-semibold">Usuario</TableHead>
                            <TableHead class="font-semibold">Descripción</TableHead>
                            <TableHead class="font-semibold">Controlador</TableHead>
                            <TableHead class="font-semibold">IP</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="bitacora in props.bitacoras" :key="bitacora.id">
                            <TableCell class="whitespace-nowrap text-muted-foreground">
                                {{ new Date(bitacora.created_at).toLocaleString('es-BO') }}
                            </TableCell>
                            <TableCell>
                                {{ bitacora.user ? `${bitacora.user.name} ${bitacora.user.lastName}` : 'Sistema' }}
                            </TableCell>
                            <TableCell>{{ bitacora.descripcion }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ bitacora.controlador }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ bitacora.ip }}</TableCell>
                        </TableRow>

                        <TableRow v-if="props.bitacoras.length === 0">
                            <TableCell colspan="5" class="py-12 text-center text-muted-foreground">
                                No hay registros de actividad todavía.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
