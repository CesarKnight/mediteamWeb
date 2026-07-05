<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Activity, BookOpen, CircleArrowLeft, Clipboard, ClipboardClock, Clock, DollarSign, FolderGit2, LayoutGrid, Megaphone, Paperclip, SquareActivity, Stethoscope, Users, Users2} from '@lucide/vue';
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
import { Bitacoraindex, Citasindex, Historiasindex, home, Pagosindex, Reportesindex, Secretariasindex, Serviciosindex } from '@/routes';
import type { NavItem } from '@/types';
import { index as usuarioIndex} from '@/actions/App/Http/Controllers/UserController';
import { index as pacienteIndex} from '@/actions/App/Http/Controllers/PacienteController';
import { index as medicoIndex} from '@/actions/App/Http/Controllers/MedicoController';
import { index as dashboard } from '@/actions/App/Http/Controllers/DashboardController';


const mainNavItems: NavItem[] = [
    {
        title: 'Panel',
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
    {
        title: 'Pagos',
        href: Pagosindex(),
        icon: DollarSign,
    },
    {
        title: 'Reportes',
        href: Reportesindex(),
        icon: Paperclip,
    },
    {
        title: 'Bitacora',
        href: Bitacoraindex(),
        icon: ClipboardClock,
    }
];

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
