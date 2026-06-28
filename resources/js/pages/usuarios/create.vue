<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';

import { Usuariosindex } from '@/routes';
import {
    create as createUser,
    store as storeUser
} from '@/actions/App/Http/Controllers/UserController';
import UserCreateForm from '@/components/user/userCreateForm.vue';

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
                <UserCreateForm
                    :form-definition="storeUser.form()"
                    :password-rules="passwordRules"
                />
            </CardContent>
        </Card>
    </div>
</template>