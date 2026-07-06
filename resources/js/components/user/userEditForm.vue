<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Form } from '@inertiajs/vue3';
import type { RouteFormDefinition } from '@/wayfinder';

defineProps<{
    formDefinition: RouteFormDefinition<'put' | 'post' | 'patch'>;
    passwordRules: string;
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
</script>

<template>
    <Form v-bind="formDefinition" v-slot="{ errors, processing }" class="flex flex-col gap-6">
        <!-- Wayfinder generates PUT forms as POST + _method spoofing -->
        <input type="hidden" name="_method" value="PUT" />

        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">Nombre</Label>
                <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name"
                    :default-value="user.name" placeholder="Nombre" />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="lastName">Apellido</Label>
                <Input id="lastName" type="text" required :tabindex="2" autocomplete="last-name" name="lastName"
                    :default-value="user.lastName" placeholder="Apellido Paterno y Materno" />
                <InputError :message="errors.lastName" />
            </div>

            <div class="grid gap-2">
                <Label for="ci">Carnet de identidad</Label>
                <Input id="ci" type="text" required :tabindex="3" name="ci" :default-value="user.ci"
                    placeholder="ej: 92781643" />
                <InputError :message="errors.ci" />
            </div>

            <div class="grid gap-2">
                <Label for="fechaNacimiento">Fecha de nacimiento</Label>
                <Input id="fechaNacimiento" type="date" required :tabindex="4" name="fechaNacimiento"
                    :default-value="user.fechaNacimiento" />
                <InputError :message="errors.fechaNacimiento" />
            </div>

            <div class="grid gap-2">
                <Label for="telefono">Teléfono</Label>
                <Input id="telefono" type="text" :tabindex="5" name="telefono" :default-value="user.telefono"
                    placeholder="telefono celular ej: 7162437" />
                <InputError :message="errors.telefono" />
            </div>

            
                
            <select id="tipo" name="tipo" :tabindex="6" hidden
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                <option value="paciente" :selected="user.tipo === 'paciente'">Paciente</option>
                <option value="medico" :selected="user.tipo === 'medico'">Médico</option>
                <option value="secretaria" :selected="user.tipo === 'secretaria'">Secretaria</option>
                <option value="administrador" :selected="user.tipo === 'administrador'">Administrador</option>
            </select>
                
            

            <div class="grid gap-2">
                <Label for="email">Correo Electrónico</Label>
                <Input id="email" type="email" required :tabindex="7" autocomplete="email" name="email"
                    :default-value="user.email" placeholder="email@example.com" />
                <InputError :message="errors.email" />
            </div>

            <!-- Password is optional on edit -->
            <div class="grid gap-2">
                <Label for="password">
                    Nueva Contraseña
                    <span class="ml-1 text-xs text-muted-foreground">(dejar en blanco para no cambiar)</span>
                </Label>
                <PasswordInput id="password" :tabindex="8" autocomplete="new-password" name="password"
                    placeholder="Nueva contraseña" :passwordrules="passwordRules" />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirmar Nueva Contraseña</Label>
                <PasswordInput id="password_confirmation" :tabindex="9" autocomplete="new-password"
                    name="password_confirmation" placeholder="Confirmar nueva contraseña"
                    :passwordrules="passwordRules" />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button type="submit" class="mt-2 w-full" tabindex="10" :disabled="processing">
                <Spinner v-if="processing" />
                Guardar cambios
            </Button>
        </div>
    </Form>
</template>