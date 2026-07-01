<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
import { LayoutList, LayoutGrid, Pencil, Trash2 } from '@lucide/vue';
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
    edit as editServicio,
    destroy as destroyServicio,
} from '@/actions/App/Http/Controllers/ServicioController';
import ServicioCards from './servicioCards.vue';

defineProps<{
    servicios: {
        id: number;
        titulo: string;
        descripcion: string;
        precio: number;
        duracion: string;
        estado: string;
    }[];
    title?: string;
}>();

const STORAGE_KEY = 'servicio-view-cards';
const showCards = ref(false);

onMounted(() => {
    showCards.value = localStorage.getItem(STORAGE_KEY) === 'true';
});

watch(showCards, (val) => {
    localStorage.setItem(STORAGE_KEY, String(val));
});

const servicioToDelete = ref<{ id: number } | null>(null);

function confirmDelete(s: { id: number }) {
    servicioToDelete.value = { id: s.id };
}

function cancelDelete() {
    servicioToDelete.value = null;
}

function executeDelete() {
    if (!servicioToDelete.value) return;
    router.delete(destroyServicio({ servicio: servicioToDelete.value.id }), {
        onFinish: () => { servicioToDelete.value = null; },
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
        <ServicioCards v-if="showCards" :servicios="servicios" />

        <!-- Table view -->
        <Card v-else class="w-full">
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/50">
                            <TableHead class="font-semibold">Título</TableHead>
                            <TableHead class="font-semibold">Descripción</TableHead>
                            <TableHead class="font-semibold">Precio</TableHead>
                            <TableHead class="font-semibold">Duración</TableHead>
                            <TableHead class="font-semibold">Estado</TableHead>
                            <TableHead class="text-right font-semibold">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="servicio in servicios" :key="servicio.id"
                            class="hover:bg-muted/40 transition-colors cursor-pointer"
                            @click="() => router.visit(editServicio({ servicio: servicio.id }))">
                            <TableCell class="font-medium">{{ servicio.titulo }}</TableCell>
                            <TableCell class="text-muted-foreground max-w-xs truncate">
                                {{ servicio.descripcion }}
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                {{ servicio.precio.toFixed(2) }} Bs.
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{ servicio.duracion }}</TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                    :class="servicio.estado === 'disponible'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                        : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'">
                                    {{ servicio.estado === 'disponible' ? 'Disponible' : 'No disponible' }}
                                </span>
                            </TableCell>
                            <TableCell class="text-right" @click.stop>
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="icon" as-child
                                         @click="() => router.visit(editServicio({ servicio: servicio.id }))">
                                        <a>
                                            <Pencil class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">Editar servicio {{ servicio.id }}</span>
                                        </a>
                                    </Button>
                                    <Button variant="ghost" size="icon" class="hover:bg-destructive/10"
                                        @click="confirmDelete(servicio)">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                        <span class="sr-only">Eliminar servicio {{ servicio.id }}</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="servicios.length === 0">
                            <TableCell colspan="6" class="py-12 text-center text-muted-foreground">
                                No hay servicios registrados aún.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

    </div>

    <Dialog :open="servicioToDelete !== null" @update:open="cancelDelete">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>¿Eliminar servicio?</DialogTitle>
                <DialogDescription>
                    Esta acción eliminará permanentemente el servicio. No se puede deshacer.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" @click="cancelDelete">Cancelar</Button>
                <Button variant="destructive" @click="executeDelete">Sí, eliminar</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>