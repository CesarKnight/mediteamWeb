<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Eye, Pencil, SquarePlus, Trash2 } from '@lucide/vue';
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
import {
    index as pacientesIndex,
    show as showPaciente,
    edit as editPaciente,
    destroy as destroyPaciente,
} from '@/actions/App/Http/Controllers/PacienteController';
import { create as createUser } from '@/actions/App/Http/Controllers/UserController';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Pacientes', href: pacientesIndex() }],
    },
});

defineProps<{
    pacientes: {
        id: number;
        estado: string;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        email: string;
    }[];
}>();

const estadoColor: Record<string, string> = {
    alta: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    baja: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
};

const pacienteToDelete = ref<{ id: number; name: string; lastName: string } | null>(null);

function confirmDelete(p: { id: number; name: string; lastName: string }) {
    pacienteToDelete.value = p;
}

function cancelDelete() {
    pacienteToDelete.value = null;
}

function executeDelete() {
    if (!pacienteToDelete.value) return;
    router.delete(destroyPaciente({ paciente: pacienteToDelete.value.id }), {
        onFinish: () => { pacienteToDelete.value = null; },
    });
}
</script>

<template>
    <Head title="Pacientes" />
    <h1 class="sr-only">Pacientes</h1>

    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Pacientes</h2>
                <p class="text-sm text-muted-foreground">
                    {{ pacientes.length }} paciente{{ pacientes.length !== 1 ? 's' : '' }} registrado{{ pacientes.length !== 1 ? 's' : '' }}
                </p>
            </div>

            <Button as-child>
                <Link :href="createUser()">
                    <SquarePlus class="mr-2 h-4 w-4" />
                    Añadir Paciente
                </Link>
            </Button>
        </div>

        <Card>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/50">
                            <TableHead class="font-semibold">Nombre</TableHead>
                            <TableHead class="font-semibold">CI</TableHead>
                            <TableHead class="font-semibold">Fecha de nacimiento</TableHead>
                            <TableHead class="font-semibold">Teléfono</TableHead>
                            <TableHead class="font-semibold">Correo electrónico</TableHead>
                            <TableHead class="font-semibold">Estado</TableHead>
                            <TableHead class="text-right font-semibold">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="paciente in pacientes"
                            :key="paciente.id"
                            class="hover:bg-muted/40 transition-colors"
                            @click="() => router.visit(showPaciente({ paciente: paciente.id }))"
                        >
                            <TableCell>
                                <div class="font-medium">{{ paciente.name }} {{ paciente.lastName }}</div>
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{ paciente.ci }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ paciente.fechaNacimiento }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ paciente.telefono ?? '—' }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ paciente.email }}</TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                    :class="estadoColor[paciente.estado] ?? 'bg-gray-100 text-gray-700'"
                                >
                                    {{ paciente.estado }}
                                </span>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                     <Button variant="ghost" size="icon" as-child>
                                        <Link :href="showPaciente({ paciente: paciente.id })">
                                            <Eye class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">Mostrar {{ paciente.name }}</span>
                                        </Link>
                                    </Button>

                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="editPaciente({ paciente: paciente.id })">
                                            <Pencil class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">Editar {{ paciente.name }}</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="hover:bg-destructive/10"
                                        @click="confirmDelete(paciente)"
                                    >
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                        <span class="sr-only">Eliminar {{ paciente.name }}</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="pacientes.length === 0">
                            <TableCell colspan="7" class="py-12 text-center text-muted-foreground">
                                No hay pacientes registrados aún.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>

    <Dialog :open="pacienteToDelete !== null" @update:open="cancelDelete">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>¿Eliminar paciente?</DialogTitle>
                <DialogDescription>
                    Estás a punto de eliminar a
                    <span class="font-medium text-foreground">
                        {{ pacienteToDelete?.name }} {{ pacienteToDelete?.lastName }}
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