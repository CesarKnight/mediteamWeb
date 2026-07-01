<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { show as showPago } from '@/actions/App/Http/Controllers/PagoController';

defineProps<{
    pagos: { id: number; total: number; metodo: string; paciente: string; servicio: string }[];
}>();
</script>

<template>
    <div class="flex flex-col divide-y divide-border">
        <p v-if="pagos.length === 0" class="py-8 text-center text-sm text-muted-foreground">
            No hay pagos pendientes.
        </p>

        <div v-for="pago in pagos" :key="pago.id"
            class="flex cursor-pointer items-center justify-between py-3 hover:bg-muted/40"
            @click="() => router.visit(showPago({ pago: pago.id }))">
            <div class="min-w-0">
                <p class="truncate text-sm font-medium">{{ pago.paciente }}</p>
                <p class="truncate text-xs text-muted-foreground">{{ pago.servicio }}</p>
            </div>
            <div class="shrink-0 text-right">
                <p class="text-sm font-semibold">{{ pago.total.toFixed(2) }} Bs.</p>
                <p class="text-xs text-muted-foreground">{{ pago.metodo === 'QR' ? 'QR' : 'Efectivo' }}</p>
            </div>
        </div>
    </div>
</template>