<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    index as permisosIndex,
    toggle as togglePermiso,
} from '@/actions/App/Http/Controllers/PermisoController';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

type RolOption = {
    id: number;
    nombre: string;
};

type Recurso = {
    nombre: string;
    operaciones: Record<string, string | null>;
};

const props = defineProps<{
    roles: RolOption[];
    operaciones: string[];
    recursos: Recurso[];
    permisosPorRol: Record<number, string[]>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Permisos', href: permisosIndex() }],
    },
});

const rolColor: Record<string, string> = {
    paciente: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
    medico: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    secretaria: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
    administrador: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
};

const operacionLabels: Record<string, string> = {
    ver: 'Ver',
    crear: 'Crear',
    editar: 'Editar',
    eliminar: 'Eliminar',
    generarQr: 'Generar QR',
};

const rolSeleccionadoId = ref<number | null>(props.roles[0]?.id ?? null);

const rolSeleccionado = computed(() =>
    props.roles.find((r) => r.id === rolSeleccionadoId.value) ?? null,
);

const permisosActivos = computed(() => {
    if (rolSeleccionadoId.value === null) {
        return new Set<string>();
    }

    return new Set(props.permisosPorRol[rolSeleccionadoId.value] ?? []);
});

function estaHabilitado(permiso: string): boolean {
    return permisosActivos.value.has(permiso);
}

function alternar(permiso: string, habilitado: boolean) {
    if (rolSeleccionadoId.value === null) {
        return;
    }

    router.patch(
        togglePermiso(rolSeleccionadoId.value).url,
        { permiso, habilitado },
        { preserveScroll: true, preserveState: true },
    );
}
</script>

<template>
    <Head title="Permisos" />

    <div class="m-6 flex flex-col gap-6">
        <Heading
            title="Permisos"
            description="Habilita o deshabilita qué puede hacer cada rol dentro del sistema."
        />

        <div class="flex flex-wrap gap-2">
            <Button
                v-for="rol in roles"
                :key="rol.id"
                :variant="rol.id === rolSeleccionadoId ? 'default' : 'outline'"
                size="sm"
                class="capitalize"
                @click="rolSeleccionadoId = rol.id"
            >
                {{ rol.nombre }}
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                        :class="rolSeleccionado ? (rolColor[rolSeleccionado.nombre] ?? 'bg-gray-100 text-gray-700') : ''"
                    >
                        {{ rolSeleccionado?.nombre }}
                    </span>
                </CardTitle>
                <CardDescription>
                    Activa un interruptor para conceder ese permiso al rol seleccionado, o desactívalo para revocarlo.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow class="bg-muted/50">
                            <TableHead class="font-semibold">Función</TableHead>
                            <TableHead
                                v-for="operacion in operaciones"
                                :key="operacion"
                                class="text-center font-semibold"
                            >
                                {{ operacionLabels[operacion] ?? operacion }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="recurso in recursos" :key="recurso.nombre">
                            <TableCell class="font-medium">{{ recurso.nombre }}</TableCell>
                            <TableCell
                                v-for="operacion in operaciones"
                                :key="operacion"
                                class="text-center"
                            >
                                <Switch
                                    v-if="recurso.operaciones[operacion]"
                                    :disabled="!puede('Permiso.editar')"
                                    :model-value="estaHabilitado(recurso.operaciones[operacion]!)"
                                    @update:model-value="(habilitado) => alternar(recurso.operaciones[operacion]!, habilitado)"
                                />
                                <span v-else class="text-muted-foreground">—</span>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
