<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import { Stethoscope } from '@lucide/vue';
import Separator from '../ui/separator/Separator.vue';

defineProps<{
    medico: {
        id: number;
        especialidad: string | null;
    };
    user: {
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        email: string;
    };
}>();
</script>

<template>
    <Card class="w-full">
        <CardHeader class="flex flex-row items-start justify-between">
            <div>
                <CardTitle class="text-xl">
                    {{ user.name }} {{ user.lastName }}
                </CardTitle>
                <Separator class="my-2"/>
                <CardDescription>{{ user.email }}</CardDescription>
            </div>
            <div class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                <Stethoscope class="h-3 w-3" />
                {{ medico.especialidad ?? 'Sin especialidad' }}
            </div>
        </CardHeader>

        <CardContent>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Carnet de identidad</dt>
                    <dd class="text-sm font-medium">{{ user.ci }}</dd>
                </div>

                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Fecha de nacimiento</dt>
                    <dd class="text-sm font-medium">{{ user.fechaNacimiento }}</dd>
                </div>

                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Teléfono</dt>
                    <dd class="text-sm font-medium">{{ user.telefono ?? '—' }}</dd>
                </div>

                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Especialidad</dt>
                    <dd class="text-sm font-medium">{{ medico.especialidad ?? '—' }}</dd>
                </div>
            </dl>
        </CardContent>
    </Card>
</template>