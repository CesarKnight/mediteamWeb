<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Eye } from '@lucide/vue';
import { computed } from 'vue';

const visitas = computed(() => usePage().props.visitasPagina);
</script>

<template>
    <footer v-if="visitas !== null" class="border-t px-4 py-3 text-center text-xs text-muted-foreground">
        Esta página ha sido visitada {{ visitas }} {{ visitas === 1 ? 'vez' : 'veces' }}.
        <Eye class="inline-block h-4 w-4" />
    </footer>
</template>
