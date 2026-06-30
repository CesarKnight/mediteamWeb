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
    show as showHistoria,
    destroy as destroyHistoria,
} from '@/actions/App/Http/Controllers/HistoriaController';

defineProps<{
    historias: {
        id: number;
        estado: string;
        paciente: {
            id: number;
            name: string;
            lastName: string;
        };
        medicoCreador: {
            id: number;
            name: string;
            lastName: string;
            especialidad: string | null;
        };
        medicosInvolucrados: {
            id: number;
            name: string;
            lastName: string;
        }[];
    }[];
}>();

const historiaToDelete = ref<{ id: number } | null>(null);

function confirmDelete(h: { id: number }) {
    historiaToDelete.value = { id: h.id };
}

function cancelDelete() {
    historiaToDelete.value = null;
}

function executeDelete() {
    if (!historiaToDelete.value) return;
    router.delete(destroyHistoria({ historia: historiaToDelete.value.id }), {
        onFinish: () => { historiaToDelete.value = null; },
    });
}
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <p v-if="historias.length === 0" class="col-span-full py-12 text-center text-muted-foreground">
            No hay historias clínicas registradas aún.
        </p>

        <Card v-for="historia in historias" :key="historia.id"
            class="cursor-pointer transition-colors hover:bg-muted/40"
            @click="() => router.visit(showHistoria({ historia: historia.id }))">
            <CardHeader class="flex flex-row items-start justify-between pb-2">
                <div>
                    <CardTitle class="text-base">
                        {{ historia.paciente.name }} {{ historia.paciente.lastName }}
                    </CardTitle>
                    <CardDescription>Historia #{{ historia.id }}</CardDescription>
                </div>
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium capitalize text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                    {{ historia.estado }}
                </span>
            </CardHeader>

            <CardContent class="grid gap-3 pb-2">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-muted-foreground">Médico creador</span>
                    <span class="text-sm font-medium">
                        {{ historia.medicoCreador.name }} {{ historia.medicoCreador.lastName }}
                    </span>
                    <span class="text-xs text-muted-foreground">
                        {{ historia.medicoCreador.especialidad ?? 'Sin especialidad' }}
                    </span>
                </div>

                <div v-if="historia.medicosInvolucrados.length > 0" class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-muted-foreground">Médicos involucrados</span>
                    <div class="flex flex-wrap gap-1">
                        <span v-for="medico in historia.medicosInvolucrados" :key="medico.id"
                            class="inline-flex items-center rounded-full bg-muted px-2 py-0.5 text-xs font-medium">
                            {{ medico.name }} {{ medico.lastName }}
                        </span>
                    </div>
                </div>
            </CardContent>

            <CardFooter class="flex justify-end gap-2 pt-2" @click.stop>
                <Button variant="ghost" size="icon" as-child @click="showHistoria({ historia: historia.id })">
                    <a>
                        <Eye class="h-4 w-4 text-muted-foreground" />
                        <span class="sr-only">Ver historia {{ historia.id }}</span>
                    </a>
                </Button>
                <Button variant="ghost" size="icon" class="hover:bg-destructive/10" @click="confirmDelete(historia)">
                    <Trash2 class="h-4 w-4 text-destructive" />
                    <span class="sr-only">Eliminar historia {{ historia.id }}</span>
                </Button>
            </CardFooter>
        </Card>
    </div>

    <Dialog :open="historiaToDelete !== null" @update:open="cancelDelete">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>¿Eliminar historia clínica?</DialogTitle>
                <DialogDescription>
                    Esta acción eliminará permanentemente la historia clínica y todos sus registros asociados.
                    No se puede deshacer.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" @click="cancelDelete">Cancelar</Button>
                <Button variant="destructive" @click="executeDelete">Sí, eliminar</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>