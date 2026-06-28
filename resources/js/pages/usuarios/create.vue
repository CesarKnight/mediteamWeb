<script setup lang="ts">
import { Form, Head} from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';

import { Usuariosindex } from '@/routes';
import { 
    create as createUser,
    store
} from '@/actions/App/Http/Controllers/UserController';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: Usuariosindex(),
            },
            {
                title: 'Crear Usuario',
                href: createUser(),
            },
        ],
    },
});

</script>

<template>
    <Head title="Register" />
    <div class="flex flex-row justify-center m-6">
        <Card class="row w-full lg:w-1/2">
        <CardHeader>
            <CardTitle>
                Registrar Nuevo Usuario
            </CardTitle>
            <CardDescription>
                Enter your email below to login to your account
            </CardDescription>
        </CardHeader>
        <CardContent>   
            <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
        >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">Nombre</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Nombre"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="lastName">Apellido</Label>
                <Input
                    id="lastName"
                    type="text"
                    required
                    :tabindex="2"
                    autocomplete="last-name"
                    name="lastName"
                    placeholder="Apellido Paterno y Materno"
                />
                <InputError :message="errors.lastName" />
            </div>

            <div class="grid gap-2">
                <Label for="ci">Carnet de identidad</Label>
                <Input
                    id="ci"
                    type="text"
                    required
                    :tabindex="3"
                    name="ci"
                    placeholder="ej: 92781643"
                />
                <InputError :message="errors.ci" />
            </div>

            <div class="grid gap-2">
                <Label for="fechaNacimiento">Fecha de nacimiento</Label>
                <Input
                    id="fechaNacimiento"
                    type="date"
                    required
                    :tabindex="4"
                    name="fechaNacimiento"
                    placeholder="Apellido Paterno y Materno"
                />
                <InputError :message="errors.fechaNacimiento" />
            </div>

            <div class="grid gap-2">
                <Label for="telefono">Telefono</Label>
                <Input
                    id="telefono"
                    type="text"
                    :tabindex="5"
                    name="telefono"
                    placeholder="telefono celular ej: 7162437"
                />
                <InputError :message="errors.telefono" />
            </div>


            <div class="grid gap-2">
                <Label for="tipo">Rol de usuario</Label>
                <select
                    id="tipo"
                    name="tipo"
                    :tabindex="6"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option value="paciente">
                        Paciente
                    </option>
                    <option value="medico">
                        Medico
                    </option>
                    <option value="secretaria">
                        Secretaria
                    </option>
                    <option value="administrador">
                        Administrador
                    </option>
                </select>
                <InputError :message="errors.tipo" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Correo Electronico</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="7"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Contraseña</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="8"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirmar Contraseña</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="9"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="10"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Crea una cuenta
            </Button>
        </div>

        <!-- <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="6"
                >Log in</TextLink
            >
        </div> -->
    </Form>
        </CardContent>
    </Card>
    </div>
</template>