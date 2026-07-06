<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Activity, BookOpen, CircleArrowLeft, Clipboard, ClipboardClock, Clock, DollarSign, FolderGit2, LayoutGrid, Megaphone, Paperclip, SquareActivity, Stethoscope, ToggleRight, Users, Users2} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { Bitacoraindex, Citasindex, Historiasindex, home, Pagosindex, Permisosindex, Reportesindex, Secretariasindex, Serviciosindex } from '@/routes';
import type { NavItem } from '@/types';
import { index as usuarioIndex} from '@/actions/App/Http/Controllers/UserController';
import { index as pacienteIndex} from '@/actions/App/Http/Controllers/PacienteController';
import { index as medicoIndex} from '@/actions/App/Http/Controllers/MedicoController';
import { index as dashboard } from '@/actions/App/Http/Controllers/DashboardController';
import { usePermisos } from '@/composables/usePermisos';

const { puede } = usePermisos();

type NavItemConLlave = NavItem & { permiso: string };

const todosLosNavItems: NavItemConLlave[] = [
    {
        title: 'Panel',
        href: dashboard(),
        icon: LayoutGrid,
        permiso: 'Dashboard.ver',
    },
    {
        title: 'Usuarios',
        href: usuarioIndex(),
        icon: Users,
        permiso: 'Usuario.ver',
    },
    {
        title: 'Pacientes',
        href: pacienteIndex(),
        icon: SquareActivity,
        permiso: 'Paciente.ver',
    },
    {
        title: 'Medicos',
        href: medicoIndex(),
        icon: Stethoscope,
        permiso: 'Medico.ver',
    },
    {
        title: 'Secretarias',
        href: Secretariasindex(),
        icon: Megaphone,
        permiso: 'Secretaria.ver',
    },
    {
        title: 'Historia Clinica',
        href: Historiasindex(),
        icon: Clipboard,
        permiso: 'Historia.ver',
    },
    {
        title: 'Servicios',
        href: Serviciosindex(),
        icon: Activity,
        permiso: 'Servicio.ver',
    },
    {
        title: 'Citas',
        href: Citasindex(),
        icon: Clock,
        permiso: 'Cita.ver',
    },
    {
        title: 'Pagos',
        href: Pagosindex(),
        icon: DollarSign,
        permiso: 'Pago.ver',
    },
    {
        title: 'Reportes',
        href: Reportesindex(),
        icon: Paperclip,
        permiso: 'Reporte.ver',
    },
    {
        title: 'Bitacora',
        href: Bitacoraindex(),
        icon: ClipboardClock,
        permiso: 'Bitacora.ver',
    },
    {
        title: 'Permisos',
        href: Permisosindex(),
        icon: ToggleRight,
        permiso: 'Permiso.ver',
    },
];

const mainNavItems = computed<NavItem[]>(() =>
    todosLosNavItems.filter((item) => puede(item.permiso)),
);

const footerNavItems: NavItem[] = [

];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="home()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
