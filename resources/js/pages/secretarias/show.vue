<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Pencil } from '@lucide/vue';
import SecretariaCard from '@/components/secretaria/secretariaCard.vue';
import {
    index as secretariasIndex,
    edit as editSecretaria,
} from '@/actions/App/Http/Controllers/SecretariaController';

defineProps<{
    secretaria: {
        id: number;
        profesion: string | null;
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
            { title: 'Secretarias', href: secretariasIndex() },
            { title: 'Ver Secretaria', href: '' },
        ],
    },
});
</script>

<template>
    <Head :title="`${user.name} ${user.lastName}`" />

    <div class="m-6 flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">
                    SECRETARIA
                </h2>
                <p class="text-sm text-muted-foreground">Ficha de la secretaria</p>
            </div>
            <Button as-child>
                <Link :href="editSecretaria({ secretaria: secretaria.id })">
                    <Pencil class="mr-2 h-4 w-4" />
                    Editar
                </Link>
            </Button>
        </div>

        <SecretariaCard :secretaria="secretaria" :user="user" />

        <!-- Future relationships go here -->
    </div>
</template>