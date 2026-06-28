<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Pencil, Trash2, UserPlus } from '@lucide/vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

import {
    create as createUser,
    destroy as destroyUser,
    edit as editUser,
    index as usuarioIndex
} from '@/actions/App/Http/Controllers/UserController';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: usuarioIndex(),
            },
        ],
    },
});

defineProps<{
    usuarios: {
        id: number;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        tipo: string;
        email: string;
    }[];
}>();

const rolColor: Record<string, string> = {
    paciente:      'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    medico:        'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    secretaria:    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
    administrador: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
};

// Delete confirmation state
const userToDelete = ref<{ id: number; name: string; lastName: string } | null>(null);

function confirmDelete(user: { id: number; name: string; lastName: string }) {
    userToDelete.value = user;
}

function cancelDelete() {
    userToDelete.value = null;
}

function executeDelete() {
    if (!userToDelete.value) return;
    router.delete(destroyUser({ user: userToDelete.value.id }), {
        onFinish: () => { userToDelete.value = null; },
    });
}
</script>

<template>
    <Head title="Usuarios" />
    <h1 class="sr-only">Usuarios</h1>

    <div class="m-6 flex flex-col gap-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Usuarios</h2>
                <p class="text-sm text-muted-foreground">
                    {{ usuarios.length }} usuario{{ usuarios.length !== 1 ? 's' : '' }} registrado{{ usuarios.length !== 1 ? 's' : '' }}
                </p>
            </div>
            <Button as-child>
                <Link :href="createUser()">
                    <UserPlus class="mr-2 h-4 w-4" />
                    Añadir usuario
                </Link>
            </Button>
        </div>

        <!-- Table -->
        <Card>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/50">
                            <TableHead class="font-semibold">Nombre</TableHead>
                            <TableHead class="font-semibold">CI</TableHead>
                            <TableHead class="font-semibold">Fecha de nacimiento</TableHead>
                            <TableHead class="font-semibold">Teléfono</TableHead>
                            <TableHead class="font-semibold">Rol</TableHead>
                            <TableHead class="font-semibold">Correo electrónico</TableHead>
                            <TableHead class="text-right font-semibold">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="user in usuarios"
                            :key="user.id"
                            class="hover:bg-muted/40 transition-colors"
                        >
                            <TableCell>
                                <div class="font-medium">{{ user.name }} {{ user.lastName }}</div>
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{ user.ci }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ user.fechaNacimiento }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ user.telefono ?? '—' }}</TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                    :class="rolColor[user.tipo] ?? 'bg-gray-100 text-gray-700'"
                                >
                                    {{ user.tipo }}
                                </span>
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{ user.email }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="editUser({ user: user.id })">
                                            <Pencil class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">Editar {{ user.name }}</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="hover:bg-destructive/10"
                                        @click="confirmDelete(user)"
                                    >
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                        <span class="sr-only">Eliminar {{ user.name }}</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <!-- Empty state -->
                        <TableRow v-if="usuarios.length === 0">
                            <TableCell colspan="7" class="py-12 text-center text-muted-foreground">
                                No hay usuarios registrados aún.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>

    <!-- Delete confirmation dialog -->
    <Dialog :open="userToDelete !== null" @update:open="cancelDelete">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>¿Eliminar usuario?</DialogTitle>
                <DialogDescription>
                    Estás a punto de eliminar a
                    <span class="font-medium text-foreground">
                        {{ userToDelete?.name }} {{ userToDelete?.lastName }}
                    </span>.
                    Esta acción no se puede deshacer.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" @click="cancelDelete">
                    Cancelar
                </Button>
                <Button variant="destructive" @click="executeDelete">
                    Sí, eliminar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>