<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
import { LayoutList, LayoutGrid, Eye, Trash2 } from '@lucide/vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    Table, TableBody, TableCell,
    TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import {
    show as showPago,
    destroy as destroyPago,
} from '@/actions/App/Http/Controllers/PagoController';
import PagoCards from './pagoCards.vue';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

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
    title?: string;
}>();

const STORAGE_KEY = 'pago-view-cards';
const showCards = ref(false);

onMounted(() => {
    showCards.value = localStorage.getItem(STORAGE_KEY) === 'true';
});

watch(showCards, (val) => {
    localStorage.setItem(STORAGE_KEY, String(val));
});

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
    <div class="flex flex-col gap-4">

        <!-- Toolbar -->
        <div class="flex items-center justify-between">
            <h3 v-if="title" class="text-lg font-semibold">{{ title }}</h3>
            <div class="flex items-center gap-2 ml-auto">
                <LayoutList class="h-4 w-4 text-muted-foreground" />
                <Switch v-model="showCards" id="view-toggle" />
                <LayoutGrid class="h-4 w-4 text-muted-foreground" />
                <Label for="view-toggle" class="sr-only">
                    Cambiar vista
                </Label>
            </div>
        </div>

        <!-- Cards view -->
        <PagoCards v-if="showCards" :pagos="pagos" />

        <!-- Table view -->
        <Card v-else class="w-full">
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/50">
                            <TableHead class="font-semibold">Paciente</TableHead>
                            <TableHead class="font-semibold">Servicio</TableHead>
                            <TableHead class="font-semibold">Total</TableHead>
                            <TableHead class="font-semibold">Método</TableHead>
                            <TableHead class="font-semibold">Estado</TableHead>
                            <TableHead class="font-semibold">Fecha</TableHead>
                            <TableHead class="text-right font-semibold">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="pago in pagos" :key="pago.id"
                            class="hover:bg-muted/40 transition-colors cursor-pointer"
                            @click="() => router.visit(showPago({ pago: pago.id }))">
                            <TableCell class="font-medium">
                                {{ pago.paciente.name }} {{ pago.paciente.lastName }}
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{ pago.servicio.titulo }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ pago.total.toFixed(2) }} Bs.</TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center rounded-full bg-muted px-2.5 py-0.5 text-xs font-medium">
                                    {{ pago.metodo === 'QR' ? 'QR' : 'Efectivo' }}
                                </span>
                            </TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': pago.estado === 'pagado',
                                        'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300': pago.estado === 'pendiente',
                                        'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': pago.estado === 'anulado',
                                    }">
                                    {{ pago.estado }}
                                </span>
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{ formatDate(pago.created_at) }}</TableCell>
                            <TableCell class="text-right" @click.stop>
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="icon" as-child @click="showPago({ pago: pago.id })">
                                        <a>
                                            <Eye class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">Ver pago {{ pago.id }}</span>
                                        </a>
                                    </Button>
                                    <Button v-if="puede('Pago.eliminar')" variant="ghost" size="icon" class="hover:bg-destructive/10"
                                        @click="confirmDelete(pago)">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                        <span class="sr-only">Eliminar pago {{ pago.id }}</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="pagos.length === 0">
                            <TableCell colspan="7" class="py-12 text-center text-muted-foreground">
                                No hay pagos registrados aún.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
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