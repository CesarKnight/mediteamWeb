<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Pencil, SquarePlus, Trash2 } from '@lucide/vue';
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
    index as secretariasIndex,
    show as showSecretaria,
    edit as editSecretaria,
    destroy as destroySecretaria,
} from '@/actions/App/Http/Controllers/SecretariaController';
import { create as createUser } from '@/actions/App/Http/Controllers/UserController';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Secretarias', href: secretariasIndex() }],
    },
});

defineProps<{
    secretarias: {
        id: number;
        profesion: string | null;
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

const secretariaToDelete = ref<{ id: number; name: string; lastName: string } | null>(null);

function confirmDelete(s: { id: number; user: { name: string; lastName: string } }) {
    secretariaToDelete.value = { id: s.id, name: s.user.name, lastName: s.user.lastName };
}

function cancelDelete() {
    secretariaToDelete.value = null;
}

function executeDelete() {
    if (!secretariaToDelete.value) return;
    router.delete(destroySecretaria({ secretaria: secretariaToDelete.value.id }), {
        onFinish: () => { secretariaToDelete.value = null; },
    });
}
</script>

<template>
    <Head title="Secretarias" />
    <h1 class="sr-only">Secretarias</h1>

    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Secretarias</h2>
                <p class="text-sm text-muted-foreground">
                    {{ secretarias.length }} secretaria{{ secretarias.length !== 1 ? 's' : '' }} registrada{{ secretarias.length !== 1 ? 's' : '' }}
                </p>
            </div>

            <Button as-child>
                <Link :href="createUser()">
                    <SquarePlus class="mr-2 h-4 w-4" />
                    Añadir Secretaria
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
                            <TableHead class="font-semibold">Profesión</TableHead>
                            <TableHead class="font-semibold">Teléfono</TableHead>
                            <TableHead class="font-semibold">Correo electrónico</TableHead>
                            <TableHead class="text-right font-semibold">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="secretaria in secretarias"
                            :key="secretaria.id"
                            class="hover:bg-muted/40 transition-colors cursor-pointer"
                            @click="() => router.visit(showSecretaria({ secretaria: secretaria.id }))"
                        >
                            <TableCell>
                                <div class="font-medium">{{ secretaria.user.name }} {{ secretaria.user.lastName }}</div>
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{ secretaria.user.ci }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ secretaria.profesion ?? '—' }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ secretaria.user.telefono ?? '—' }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ secretaria.user.email }}</TableCell>
                            <TableCell class="text-right" @click.stop>
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="editSecretaria({ secretaria: secretaria.id })">
                                            <Pencil class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">Editar {{ secretaria.user.name }}</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="hover:bg-destructive/10"
                                        @click="confirmDelete(secretaria)"
                                    >
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                        <span class="sr-only">Eliminar {{ secretaria.user.name }}</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="secretarias.length === 0">
                            <TableCell colspan="6" class="py-12 text-center text-muted-foreground">
                                No hay secretarias registradas aún.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>

    <Dialog :open="secretariaToDelete !== null" @update:open="cancelDelete">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>¿Eliminar secretaria?</DialogTitle>
                <DialogDescription>
                    Estás a punto de eliminar a
                    <span class="font-medium text-foreground">
                        {{ secretariaToDelete?.name }} {{ secretariaToDelete?.lastName }}
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