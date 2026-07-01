<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Eye, Trash2 } from '@lucide/vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import {
    show as showPago,
    destroy as destroyPago,
} from '@/actions/App/Http/Controllers/PagoController';

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

function formatDate(dateStr: string) {
    return new Intl.DateTimeFormat('es-BO', {
        day: '2-digit', month: '2-digit', year: 'numeric',
    }).format(new Date(dateStr));
}

const pagoToDelete = ref<{ id: number } | null>(null);

function confirmDelete(p: { id: number }) {
    pagoToDelete.value = { id: p.id };
}

function cancelDelete() {
    pagoToDelete.value = null;
}

function executeDelete() {
    if (!pagoToDelete.value) return;
    router.delete(destroyPago({ pago: pagoToDelete.value.id }), {
        onFinish: () => { pagoToDelete.value = null; },
    });
}
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <p v-if="pagos.length === 0" class="col-span-full py-12 text-center text-muted-foreground">
            No hay pagos registrados aún.
        </p>

        <Card v-for="pago in pagos" :key="pago.id"
            class="cursor-pointer transition-colors hover:bg-muted/40"
            @click="() => router.visit(showPago({ pago: pago.id }))">
            <CardHeader class="flex flex-row items-start justify-between pb-2">
                <div>
                    <CardTitle class="text-base">
                        {{ pago.paciente.name }} {{ pago.paciente.lastName }}
                    </CardTitle>
                    <CardDescription>Pago #{{ pago.id }} · {{ formatDate(pago.created_at) }}</CardDescription>
                </div>
                <span
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                    :class="pago.estado === 'pagado'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300'">
                    {{ pago.estado }}
                </span>
            </CardHeader>

            <CardContent class="grid gap-3 pb-2">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-muted-foreground">Servicio</span>
                    <span class="text-sm font-medium">{{ pago.servicio.titulo }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-muted-foreground">Total</span>
                        <span class="text-sm font-semibold">{{ pago.total.toFixed(2) }} Bs.</span>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-muted px-2 py-0.5 text-xs font-medium">
                        {{ pago.metodo === 'QR' ? 'QR' : 'Efectivo' }}
                    </span>
                </div>
            </CardContent>

            <CardFooter class="flex justify-end gap-2 pt-2" @click.stop>
                <Button variant="ghost" size="icon" as-child @click="showPago({ pago: pago.id })">
                    <a>
                        <Eye class="h-4 w-4 text-muted-foreground" />
                        <span class="sr-only">Ver pago {{ pago.id }}</span>
                    </a>
                </Button>
                <Button variant="ghost" size="icon" class="hover:bg-destructive/10" @click="confirmDelete(pago)">
                    <Trash2 class="h-4 w-4 text-destructive" />
                    <span class="sr-only">Eliminar pago {{ pago.id }}</span>
                </Button>
            </CardFooter>
        </Card>
    </div>

    <Dialog :open="pagoToDelete !== null" @update:open="cancelDelete">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>¿Eliminar pago?</DialogTitle>
                <DialogDescription>
                    Esta acción eliminará permanentemente el registro del pago. No se puede deshacer.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" @click="cancelDelete">Cancelar</Button>
                <Button variant="destructive" @click="executeDelete">Sí, eliminar</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>