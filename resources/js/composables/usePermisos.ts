import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Auth } from '@/types/auth';

export function usePermisos() {
    const page = usePage();

    const permisos = computed(() => new Set((page.props.auth as Auth).permisos ?? []));

    function puede(permiso: string): boolean {
        return permisos.value.has(permiso);
    }

    return { permisos, puede };
}
