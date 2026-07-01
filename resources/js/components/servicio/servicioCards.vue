<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Pencil, Trash2 } from '@lucide/vue';
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
    edit as editServicio,
    destroy as destroyServicio,
} from '@/actions/App/Http/Controllers/ServicioController';

defineProps<{
    servicios: {
        id: number;
        titulo: string;
        descripcion: string;
        precio: number;
        duracion: string;
        estado: string;
    }[];
}>();

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
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <p v-if="servicios.length === 0" class="col-span-full py-12 text-center text-muted-foreground">
            No hay servicios registrados aún.
        </p>

        <Card v-for="servicio in servicios" :key="servicio.id"
            class="cursor-pointer transition-colors hover:bg-muted/40"
            @click="() => router.visit(editServicio({ servicio: servicio.id }))">
            <CardHeader class="flex flex-row items-start justify-between pb-2">
                <div>
                    <CardTitle class="text-base">{{ servicio.titulo }}</CardTitle>
                    <CardDescription>{{ servicio.duracion }}</CardDescription>
                </div>
                <span
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                    :class="servicio.estado === 'disponible'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                        : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'">
                    {{ servicio.estado === 'disponible' ? 'Disponible' : 'No disponible' }}
                </span>
            </CardHeader>

            <CardContent class="grid gap-3 pb-2">
                <p class="text-sm text-muted-foreground line-clamp-3">
                    {{ servicio.descripcion }}
                </p>
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-muted-foreground">Precio</span>
                    <span class="text-sm font-medium">{{ servicio.precio.toFixed(2) }} Bs.</span>
                </div>
            </CardContent>

            <CardFooter class="flex justify-end gap-2 pt-2" @click.stop>
                <Button variant="ghost" size="icon" as-child  @click="() => router.visit(editServicio({ servicio: servicio.id }))">
                    <a>
                        <Pencil class="h-4 w-4 text-muted-foreground" />
                        <span class="sr-only">Editar servicio {{ servicio.id }}</span>
                    </a>
                </Button>
                <Button variant="ghost" size="icon" class="hover:bg-destructive/10" @click="confirmDelete(servicio)">
                    <Trash2 class="h-4 w-4 text-destructive" />
                    <span class="sr-only">Eliminar servicio {{ servicio.id }}</span>
                </Button>
            </CardFooter>
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