<script setup lang="ts">
import Card from '../ui/card/Card.vue';
import CardContent from '../ui/card/CardContent.vue';
import CardDescription from '../ui/card/CardDescription.vue';
import CardHeader from '../ui/card/CardHeader.vue';
import CardTitle from '../ui/card/CardTitle.vue';

defineProps<{
    pagoQr: {
        id: number;
        estado: string;
        expiracion: string | null;
        qr_base64: string | null;
    };
}>();

function formatExpiracion(dateStr: string | null) {
    if (!dateStr) return null;
    return new Intl.DateTimeFormat('es-BO', {
        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit',
    }).format(new Date(dateStr));
}
</script>

<template>
    <Card class="w-full lg:w-1/2 mt-6">
        <CardHeader>
            <CardTitle>Código QR de pago</CardTitle>
            <CardDescription>
                <span v-if="pagoQr.estado === 'pendiente'">Esperando confirmación del pago...</span>
                <span v-else-if="pagoQr.estado === 'exitoso'">Pago confirmado.</span>
                <span v-else>QR anulado.</span>
            </CardDescription>
        </CardHeader>
        <CardContent class="flex flex-col items-center gap-4">
            <img v-if="pagoQr.qr_base64 && pagoQr.estado === 'pendiente'"
                :src="`data:image/png;base64,${pagoQr.qr_base64}`"
                alt="Código QR de pago"
                class="h-64 w-64 rounded-md border border-input" />

            <span
                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                :class="{
                    'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300': pagoQr.estado === 'pendiente',
                    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': pagoQr.estado === 'exitoso',
                    'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': pagoQr.estado === 'anulado',
                }">
                {{ pagoQr.estado }}
            </span>

            <p v-if="pagoQr.expiracion && pagoQr.estado === 'pendiente'" class="text-xs text-muted-foreground">
                Expira: {{ formatExpiracion(pagoQr.expiracion) }}
            </p>
        </CardContent>
    </Card>
</template>