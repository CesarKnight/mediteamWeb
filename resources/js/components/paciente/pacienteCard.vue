<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';

defineProps<{
    paciente: {
        id: number;
        estado: string;
        name: string;
        lastName: string;
        ci: string;
        fechaNacimiento: string;
        telefono: string;
        email: string;
    };
}>();

const estadoColor: Record<string, string> = {
    alta: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    baja: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
};
</script>

<template>
    <Card class="w-full">
        <CardHeader class="flex flex-row items-start justify-between">
            <div>
                <CardTitle class="text-xl">
                    {{ paciente.name }} {{ paciente.lastName }}
                </CardTitle>
                <CardDescription>{{ paciente.email }}</CardDescription>
            </div>
            <span
                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                :class="estadoColor[paciente.estado] ?? 'bg-gray-100 text-gray-700'"
            >
                {{ paciente.estado }}
            </span>
        </CardHeader>

        <CardContent>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Carnet de identidad</dt>
                    <dd class="text-sm font-medium">{{ paciente.ci }}</dd>
                </div>

                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Fecha de nacimiento</dt>
                    <dd class="text-sm font-medium">{{ paciente.fechaNacimiento }}</dd>
                </div>

                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Teléfono</dt>
                    <dd class="text-sm font-medium">{{ paciente.telefono ?? '—' }}</dd>
                </div>

                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-medium text-muted-foreground">Correo electrónico</dt>
                    <dd class="text-sm font-medium">{{ paciente.email }}</dd>
                </div>
            </dl>
        </CardContent>
    </Card>
</template>