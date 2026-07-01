<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as serviciosIndex,
    update as updateServicio,
} from '@/actions/App/Http/Controllers/ServicioController';

const props = defineProps<{
    servicio: {
        id: number;
        titulo: string;
        descripcion: string;
        precio: number;
        duracion: string;
        estado: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Servicios', href: serviciosIndex() },
            { title: `Editar servicio`, href: '' },
        ],
    },
});
</script>

<template>
    <Head :title="`Editar ${servicio.titulo}`" />

    <div class="flex flex-row justify-center m-6">
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Editar Servicio</CardTitle>
                <CardDescription>{{ servicio.titulo }}</CardDescription>
            </CardHeader>
            <CardContent>
                <Form v-bind="updateServicio.form({ servicio: servicio.id })" v-slot="{ errors, processing }"
                    class="flex flex-col gap-6">
                    <div class="grid gap-6">

                        <div class="grid gap-2">
                            <Label for="titulo">Título</Label>
                            <input id="titulo" name="titulo" type="text" required :defaultValue="servicio.titulo"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                            <InputError :message="errors.titulo" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="descripcion">Descripción</Label>
                            <textarea id="descripcion" name="descripcion" required
                                :defaultValue="servicio.descripcion"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                            <InputError :message="errors.descripcion" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="precio">Precio (Bs.)</Label>
                                <input id="precio" name="precio" type="number" step="0.01" required
                                    :defaultValue="servicio.precio"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                                <InputError :message="errors.precio" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="duracion">Duración</Label>
                                <input id="duracion" name="duracion" type="text" required
                                    :defaultValue="servicio.duracion"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                                <InputError :message="errors.duracion" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="estado">Estado</Label>
                            <select id="estado" name="estado" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="disponible" :selected="servicio.estado === 'disponible'">Disponible</option>
                                <option value="no" :selected="servicio.estado === 'no'">No disponible</option>
                            </select>
                            <InputError :message="errors.estado" />
                        </div>

                        <Button type="submit" class="mt-2 w-full" :disabled="processing">
                            <Spinner v-if="processing" />
                            Guardar cambios
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>