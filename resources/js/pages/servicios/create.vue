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
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as serviciosIndex,
    store as storeServicio,
} from '@/actions/App/Http/Controllers/ServicioController';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Servicios', href: serviciosIndex() },
            { title: 'Añadir servicio', href: '' },
        ],
    },
});
</script>

<template>
    <Head title="Añadir Servicio" />

    <div class="flex flex-row justify-center m-6">
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Añadir Servicio</CardTitle>
            </CardHeader>
            <CardContent>
                <Form v-bind="storeServicio.form()" v-slot="{ errors, processing }"
                    class="flex flex-col gap-6">
                    <div class="grid gap-6">

                        <div class="grid gap-2">
                            <Label for="titulo">Título</Label>
                            <input id="titulo" name="titulo" type="text" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                            <InputError :message="errors.titulo" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="descripcion">Descripción</Label>
                            <textarea id="descripcion" name="descripcion" required
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                            <InputError :message="errors.descripcion" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="precio">Precio (Bs.)</Label>
                                <input id="precio" name="precio" type="number" step="0.01" required
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                                <InputError :message="errors.precio" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="duracion">Duración</Label>
                                <input id="duracion" name="duracion" type="text" placeholder="ej. 30 min" required
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                                <InputError :message="errors.duracion" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="estado">Estado</Label>
                            <select id="estado" name="estado" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="disponible" selected>Disponible</option>
                                <option value="no">No disponible</option>
                            </select>
                            <InputError :message="errors.estado" />
                        </div>

                        <Button type="submit" class="mt-2 w-full" :disabled="processing">
                            <Spinner v-if="processing" />
                            Crear servicio
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>