<script setup lang="ts">

import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Pencil, Trash2 } from '@lucide/vue';
import {
    Table, TableBody, TableCell,
    TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import {
    show as showMedico,
    edit as editMedico,
    destroy as destroyMedico,
} from '@/actions/App/Http/Controllers/MedicoController';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

defineProps<{
    medicos: {
        id: number;
        especialidad: string | null;
        user: {
            id: number;
            name: string;
            lastName: string;
            ci: string;
            fechaNacimiento: string;
            telefono: string;
            email: string;
        };
    }[];
}>();


const medicoToDelete = ref<{ id: number; name: string; lastName: string } | null>(null);

function confirmDelete(m: { id: number; user: { name: string; lastName: string } }) {
    medicoToDelete.value = { id: m.id, name: m.user.name, lastName: m.user.lastName };
}

function cancelDelete() {
    medicoToDelete.value = null;
}

function executeDelete() {
    if (!medicoToDelete.value) return;
    router.delete(destroyMedico({ medico: medicoToDelete.value.id }), {
        onFinish: () => { medicoToDelete.value = null; },
    });
}
</script>
<template>

    <Table>
        <TableHeader>
            <TableRow class="bg-muted/50">
                <TableHead class="font-semibold">Nombre</TableHead>
                <TableHead class="font-semibold">CI</TableHead>
                <TableHead class="font-semibold">Especialidad</TableHead>
                <TableHead class="font-semibold">Teléfono</TableHead>
                <TableHead class="font-semibold">Correo electrónico</TableHead>
                <TableHead class="text-right font-semibold">Acciones</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="medico in medicos" :key="medico.id"
                class="hover:bg-muted/40 transition-colors cursor-pointer"
                @click="() => router.visit(showMedico({ medico: medico.id }))">
                <TableCell>
                    <div class="font-medium">{{ medico.user.name }} {{ medico.user.lastName }}</div>
                </TableCell>
                <TableCell class="text-muted-foreground">{{ medico.user.ci }}</TableCell>
                <TableCell class="text-muted-foreground">{{ medico.especialidad ?? '—' }}</TableCell>
                <TableCell class="text-muted-foreground">{{ medico.user.telefono ?? '—' }}</TableCell>
                <TableCell class="text-muted-foreground">{{ medico.user.email }}</TableCell>
                <TableCell class="text-right" @click.stop>
                    <div class="flex justify-end gap-2">
                        <Button v-if="puede('Medico.editar')" variant="ghost" size="icon" as-child>
                            <Link :href="editMedico({ medico: medico.id })">
                                <Pencil class="h-4 w-4 text-muted-foreground" />
                                <span class="sr-only">Editar {{ medico.user.name }}</span>
                            </Link>
                        </Button>
                        <Button v-if="puede('Medico.eliminar')" variant="ghost" size="icon" class="hover:bg-destructive/10"
                            @click="confirmDelete(medico)">
                            <Trash2 class="h-4 w-4 text-destructive" />
                            <span class="sr-only">Eliminar {{ medico.user.name }}</span>
                        </Button>
                    </div>
                </TableCell>
            </TableRow>

            <TableRow v-if="medicos.length === 0">
                <TableCell colspan="6" class="py-12 text-center text-muted-foreground">
                    No hay médicos registrados aún.
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
    <Dialog :open="medicoToDelete !== null" @update:open="cancelDelete">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>¿Eliminar médico?</DialogTitle>
                <DialogDescription>
                    Estás a punto de eliminar a
                    <span class="font-medium text-foreground">
                        {{ medicoToDelete?.name }} {{ medicoToDelete?.lastName }}
                    </span>.
                    Esto también eliminará su cuenta de usuario. Esta acción no se puede deshacer.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" @click="cancelDelete">Cancelar</Button>
                <Button variant="destructive" @click="executeDelete">Sí, eliminar</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>