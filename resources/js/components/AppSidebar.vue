<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Activity, BookOpen, Clipboard, Clock, FolderGit2, LayoutGrid, Megaphone, SquareActivity, Stethoscope, Users} from '@lucide/vue';
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
import { Citasindex, dashboard, Historiasindex, Secretariasindex, Serviciosindex } from '@/routes';
import type { NavItem } from '@/types';
import { index as usuarioIndex} from '@/actions/App/Http/Controllers/UserController';
import { index as pacienteIndex} from '@/actions/App/Http/Controllers/PacienteController';
import { index as medicoIndex} from '@/actions/App/Http/Controllers/MedicoController';


const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Usuarios',
        href: usuarioIndex(),
        icon: Users,
    },
    {
        title: 'Pacientes',
        href: pacienteIndex(),
        icon: SquareActivity,
    },
    {
        title: 'Medicos',
        href: medicoIndex(),
        icon: Stethoscope,
    },
    {
        title: 'Secretarias',
        href: Secretariasindex(),
        icon: Megaphone,
    },
    {
        title: 'Historia Clinica',
        href: Historiasindex(),
        icon: Clipboard,
    },
    {
        title: 'Servicios',
        href: Serviciosindex(),
        icon: Activity,
    },
    {
        title: 'Citas',
        href: Citasindex(),
        icon: Clock,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
