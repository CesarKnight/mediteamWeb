<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Configuración de perfil',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Configuración de perfil" />

    <h1 class="sr-only">Configuración de perfil</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Perfil"
            description="Actualiza tu nombre y correo electrónico"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Nombre</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    placeholder="Nombre completo"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="lastName">Apellido</Label>
                <Input
                    id="lastName"
                    type="text"
                    required
                    :tabindex="2"
                    :default-value="user.lastName"
                    class="mt-1 block w-full"
                    autocomplete="last-name"
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
                    :default-value="user.ci"
                    class="mt-1 block w-full"
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
                    :default-value="user.fechaNacimiento"
                    class="mt-1 block w-full"
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
                    :default-value="user.telefono"
                    class="mt-1 block w-full"
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
                    :default-value="user.tipo"
                    disabled
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
                    :default-value="user.email"
                    class="mt-1 block w-full"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div v-if="page.props.mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    Tu email no esta verificado.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Click aqui para reenviar el correo de verificacion.
                    </Link>
                </p>

                <div
                    v-if="page.props.status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Un nuevo enlace de verificación ha sido enviado a tu dirección de email.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button"
                    >Guardar</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
