<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import {
    index as usuariosIndex,
    edit as editUser,
    update as updateUser,
} from '@/actions/App/Http/Controllers/UserController';
import UserEditForm from '@/components/user/userEditForm.vue';

const props = defineProps<{
    passwordRules: string;
    user: {
        id: number;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        tipo:string;
        email: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Usuarios', href: usuariosIndex() },
            { title: 'Editar Usuario', href: '' },
        ],
    },
});
</script>

<template>
    <Head title="Editar Usuario" />
    <div class="flex flex-row justify-center m-6">
        <Card class="w-full lg:w-1/2">
            <CardHeader>
                <CardTitle>Editar Usuario</CardTitle>
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