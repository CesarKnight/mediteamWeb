<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    create as createPago,
    index as pagosIndex,
} from '@/actions/App/Http/Controllers/PagoController';
import PagoTable from '@/components/pago/pagoTable.vue';
import { CircleDollarSign } from '@lucide/vue';
import Button from '@/components/ui/button/Button.vue';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Pagos', href: pagosIndex() }],
    },
});

defineProps<{
    pagos: {
        id: number;
        total: number;
        estado: string;
        metodo: string;
        created_at: string;
        paciente: { id: number; name: string; lastName: string };
        servicio: { id: number; titulo: string; precio: number };
    }[];
}>();
</script>

<template>
    <Head title="Pagos" />
    <h1 class="sr-only">Pagos</h1>
    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Pagos</h2>
                <p class="text-sm text-muted-foreground">
                    {{ pagos.length }} pago{{ pagos.length !== 1 ? 's' : '' }} registrado{{ pagos.length !== 1 ? 's' : '' }}
                </p>
            </div>
            <Button v-if="puede('Pago.crear')" as-child>
                <Link :href="createPago()">
                    <CircleDollarSign class="mr-2 h-4 w-4" />
                    Registrar pago
                </Link>
            </Button>
        </div>
        <PagoTable :pagos="pagos" />
    </div>
</template>