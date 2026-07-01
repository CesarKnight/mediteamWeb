<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { QrCode } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as pagosIndex,
} from '@/actions/App/Http/Controllers/PagoController';
import { computed, ref } from 'vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import { store as storePagoQr } from '@/actions/App/Http/Controllers/PagoQrController';
import PagoQrCard from '@/components/pagoQr/pagoQrCard.vue';

const props = defineProps<{
    pago: {
        id: number;
        total: number;
        estado: string;
        metodo: string;
        created_at: string;
        paciente: { id: number; name: string; lastName: string };
        servicio: { id: number; titulo: string; precio: number };
        pagoQrs: { id: number; estado: string; expiracion: string | null; qr_base64: string | null }[];
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pagos', href: pagosIndex() },
            { title: 'Ver pago', href: '' },
        ],
    },
});

function formatDate(dateStr: string) {
    return new Intl.DateTimeFormat('es-BO', {
        day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
    }).format(new Date(dateStr));
}


// funciones para pago qr

const latestPagoQr = computed(() => {
    const qrs = props.pago.pagoQrs;
    return qrs.length > 0 ? qrs[qrs.length - 1] : null;
});

const creatingQr = ref(false);

function generarQr() {
    creatingQr.value = true;
    router.post(storePagoQr({ pago: props.pago.id }), {}, {
        onFinish: () => { creatingQr.value = false; },
    });
}
</script>

<template>

    <Head :title="`Pago #${pago.id}`" />

    <div class="flex flex-col items-center m-6">
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Pago #{{ pago.id }}</CardTitle>
                <CardDescription>Registrado el {{ formatDate(pago.created_at) }}</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-6">

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-muted-foreground">Paciente</span>
                        <span class="text-sm font-medium">
                            {{ pago.paciente.name }} {{ pago.paciente.lastName }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-muted-foreground">Servicio</span>
                        <span class="text-sm font-medium">{{ pago.servicio.titulo }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-muted-foreground">Total</span>
                        <span class="text-lg font-semibold">{{ pago.total.toFixed(2) }} Bs.</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-muted-foreground">Método</span>
                        <span class="text-sm font-medium">{{ pago.metodo === 'QR' ? 'QR' : 'Efectivo' }}</span>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-muted-foreground">Estado</span>
                    <span
                        class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                        :class="{
                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': pago.estado === 'pagado',
                            'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300': pago.estado === 'pendiente',
                            'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': pago.estado === 'anulado',
                        }">
                        {{ pago.estado }}
                    </span>
                </div>

                <div v-if="pago.metodo === 'QR'" class="border-t border-input pt-4">
                    <Button v-if="!latestPagoQr || latestPagoQr.estado === 'anulado'" type="button"
                        :disabled="creatingQr" @click="generarQr" class="w-full justify-center">
                        <Spinner v-if="creatingQr" />
                        <QrCode class="mr-2 h-4 w-4" />
                        Generar código QR
                    </Button>
                    <p v-else class="text-xs text-muted-foreground">
                        Ya existe un código QR {{ latestPagoQr.estado === 'pendiente' ? 'pendiente' : 'confirmado' }}
                        para este pago.
                    </p>
                </div>

            </CardContent>
        </Card>

        <PagoQrCard v-if="latestPagoQr" :pago-qr="latestPagoQr" />
    </div>
</template>