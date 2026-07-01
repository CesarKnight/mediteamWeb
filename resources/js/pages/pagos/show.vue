<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
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

const props = defineProps<{
    pago: {
        id: number;
        total: number;
        estado: string;
        metodo: string;
        created_at: string;
        paciente: { id: number; name: string; lastName: string };
        servicio: { id: number; titulo: string; precio: number };
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
</script>

<template>
    <Head :title="`Pago #${pago.id}`" />

    <div class="flex flex-row justify-center m-6">
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
                        :class="pago.estado === 'pagado'
                            ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300'">
                        {{ pago.estado }}
                    </span>
                </div>

                <div v-if="pago.metodo === 'QR'" class="border-t border-input pt-4">
                    <Button type="button" disabled variant="outline" title="Próximamente">
                        <QrCode class="mr-2 h-4 w-4" />
                        Registrar pago QR
                    </Button>
                    <p class="mt-2 text-xs text-muted-foreground">
                        El seguimiento de pagos QR estará disponible próximamente.
                    </p>
                </div>

            </CardContent>
        </Card>
    </div>
</template>