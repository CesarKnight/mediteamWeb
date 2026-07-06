<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
import { LayoutList, LayoutGrid } from '@lucide/vue';
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
import { Eye, Trash2 } from '@lucide/vue';
import { router } from '@inertiajs/vue3';
import {
    show as showHistoria,
    destroy as destroyHistoria,
} from '@/actions/App/Http/Controllers/HistoriaController';
import HistoriaCards from './historiaCards.vue';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

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
    title?: string;
}>();

const STORAGE_KEY = 'historia-view-cards';
const showCards = ref(false);

onMounted(() => {
    showCards.value = localStorage.getItem(STORAGE_KEY) === 'true';
});

watch(showCards, (val) => {
    localStorage.setItem(STORAGE_KEY, String(val));
});

function toggleView(val: boolean) {
    showCards.value = val;
    localStorage.setItem(STORAGE_KEY, String(val));
}

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
        <HistoriaCards v-if="showCards" :historias="historias" />

        <!-- Table view -->
        <Card v-else class="w-full">
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/50">
                            <TableHead class="font-semibold">Paciente</TableHead>
                            <TableHead class="font-semibold">Médico creador</TableHead>
                            <TableHead class="font-semibold">Especialidad</TableHead>
                            <TableHead class="font-semibold">Médicos involucrados</TableHead>
                            <TableHead class="font-semibold">Estado</TableHead>
                            <TableHead class="text-right font-semibold">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="historia in historias" :key="historia.id"
                            class="hover:bg-muted/40 transition-colors cursor-pointer"
                            @click="() => router.visit(showHistoria({ historia: historia.id }))">
                            <TableCell>
                                <div class="font-medium">
                                    {{ historia.paciente.name }} {{ historia.paciente.lastName }}
                                </div>
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                {{ historia.medicoCreador.name }} {{ historia.medicoCreador.lastName }}
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                {{ historia.medicoCreador.especialidad ?? '—' }}
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="medico in historia.medicosInvolucrados" :key="medico.id"
                                        class="inline-flex items-center rounded-full bg-muted px-2 py-0.5 text-xs font-medium">
                                        {{ medico.name }} {{ medico.lastName }}
                                    </span>
                                    <span v-if="historia.medicosInvolucrados.length === 0"
                                        class="text-sm text-muted-foreground">
                                        —
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium capitalize text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    {{ historia.estado }}
                                </span>
                            </TableCell>
                            <TableCell class="text-right" @click.stop>
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="icon" as-child
                                        @click="() => router.visit(showHistoria({ historia: historia.id }))">
                                        <a>
                                            <Eye class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">Ver historia {{ historia.id }}</span>
                                        </a>
                                    </Button>
                                    <Button v-if="puede('Historia.eliminar')" variant="ghost" size="icon" class="hover:bg-destructive/10"
                                        @click="confirmDelete(historia)">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                        <span class="sr-only">Eliminar historia {{ historia.id }}</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="historias.length === 0">
                            <TableCell colspan="6" class="py-12 text-center text-muted-foreground">
                                No hay historias clínicas registradas aún.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
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