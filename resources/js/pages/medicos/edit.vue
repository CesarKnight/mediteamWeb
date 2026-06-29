<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import InputError from '@/components/InputError.vue';
import UserEditForm from '@/components/user/userEditForm.vue';

import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as medicosIndex,
    updateEspecialidad,
} from '@/actions/App/Http/Controllers/MedicoController';
import { update as updateUser } from '@/actions/App/Http/Controllers/UserController';
import { RouteDefinition } from '@/wayfinder';

const props = defineProps<{
    passwordRules: string;
    medico: {
        id: number;
        especialidad: string | null;
    };
    user: {
        id: number;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        tipo: string;
        email: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Médicos', href: medicosIndex() },
            { title: 'Editar Médico', href: '' },
        ],
    },
});

</script>

<template>
    <Head title="Editar Médico" />

    <div class="flex flex-col items-center gap-6 m-6">

        <!-- Especialidad form -->
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Especialidad</CardTitle>
                <CardDescription>
                    Especialización médica de {{ user.name }} {{ user.lastName }}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="updateEspecialidad.form({ medico: medico.id })"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-4"
                >
                    <div class="grid gap-2">
                        <Label for="especialidad">Especialidad</Label>
                        <Input
                            id="especialidad"
                            name="especialidad"
                            type="text"
                            :default-value="medico.especialidad ?? ''"
                            placeholder="ej: Cardiología"
                        />
                        <InputError :message="errors.especialidad" />
                    </div>

                    <Button type="submit" class="w-full" :disabled="processing">
                        <Spinner v-if="processing" />
                        Actualizar especialidad
                    </Button>
                </Form>
            </CardContent>
        </Card>
        <!-- User form -->
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Datos Personales</CardTitle>
                <CardDescription>
                    Modificando datos de {{ user.name }} {{ user.lastName }}
                </CardDescription>
            </CardHeader>
            <CardContent>
                
                <UserEditForm
                    :form-definition="updateUser.form({user: user.id})"
                    :user="user"
                    :password-rules="passwordRules"
                />
            </CardContent>
        </Card>

    </div>
</template>