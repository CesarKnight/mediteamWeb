<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';

defineProps<{
    icon: object;
    label: string;
    value: string;
    accentClass?: string;
}>();
</script>

<template>
    <Card>
        <CardContent class="flex items-center gap-3 py-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                :class="accentClass ?? 'bg-muted'">
                <component :is="icon" class="h-5 w-5" />
            </div>
            <div class="min-w-0">
                <p class="truncate text-xs font-medium text-muted-foreground">{{ label }}</p>
                <p class="truncate text-lg font-semibold">{{ value }}</p>
            </div>
        </CardContent>
    </Card>
</template>