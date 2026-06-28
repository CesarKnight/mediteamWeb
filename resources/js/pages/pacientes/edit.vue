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
    index as pacientesIndex,
    edit as editPaciente,
    update as updatePaciente,
    updateEstado as updatePacienteEstado,
} from '@/actions/App/Http/Controllers/PacienteController';
import { update as updateUser } from '@/actions/App/Http/Controllers/UserController';
import UserEditForm from '@/components/user/userEditForm.vue';

const props = defineProps<{
    passwordRules: string;
    estados: string[];
    paciente: {
        id: number;
        estado: string;
    };
    user: {
        id: number;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        email: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pacientes', href: pacientesIndex() },
            { title: 'Editar Paciente', href: '' },
        ],
    },
});
</script>

<template>
    <Head title="Editar Paciente" />

    <div class="flex flex-col items-center gap-6 m-6">

        <!-- Estado form -->
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Estado del Paciente</CardTitle>
                <CardDescription>
                    Cambia el estado de {{ user.name }} {{ user.lastName }}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="updatePacienteEstado.form({ paciente: paciente.id })"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-4"
                >
                    <div class="grid gap-2">
                        <Label for="estado">Estado</Label>
                        <select
                            id="estado"
                            name="estado"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option
                                v-for="estado in estados"
                                :key="estado"
                                :value="estado"
                                :selected="paciente.estado === estado"
                                class="capitalize"
                            >
                                {{ estado }}
                            </option>
                        </select>
                        <InputError :message="errors.estado" />
                    </div>

                    <Button type="submit" class="w-full" :disabled="processing">
                        <Spinner v-if="processing" />
                        Actualizar estado
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
                    :form-definition="updateUser.form({ user: user.id })"
                    :user="user"
                    :password-rules="passwordRules"
                />
            </CardContent>
        </Card>
    </div>
</template>