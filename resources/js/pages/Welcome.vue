<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Citasindex, dashboard, login, register } from '@/routes';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Activity,
    ArrowRight,
    CalendarCheck,
    Clock,
    HeartPulse,
    Mail,
    MapPin,
    Phone,
    Stethoscope,
    Syringe,
} from '@lucide/vue';

const visitas = computed(() => usePage().props.visitasPagina);

const servicios = [
    {
        icon: Stethoscope,
        titulo: 'Consultas generales',
        descripcion:
            'Atención médica primaria para toda la familia: revisiones, chequeos y seguimiento de tu salud.',
    },
    {
        icon: Activity,
        titulo: 'Especialistas renales',
        descripcion:
            'Nefrólogos dedicados al diagnóstico y tratamiento de enfermedades del riñón y del sistema urinario.',
    },
    {
        icon: Syringe,
        titulo: 'Enfermería',
        descripcion:
            'Personal de enfermería para curaciones, inyectables, control de signos vitales y cuidado continuo.',
    },
];

const doctores = [
    {
        nombre: 'Dra. Carla Saravia',
        especialidad: 'Medicina general',
        foto: 'images/clinica/doctores/carla-saravia.jpg',
    },
    {
        nombre: 'Dr. Jorge Fernández',
        especialidad: 'Nefrología',
        foto: 'images/clinica/doctores/jorge-fernandez.jpg',
    },
    {
        nombre: 'Lic. Ana Quispe',
        especialidad: 'Enfermería',
        foto: 'images/clinica/doctores/ana-quispe.jpg',
    },
];
</script>

<template>
    <Head title="Clínica Mediteam">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-background text-foreground">
        <header
            class="sticky top-0 z-40 border-b bg-background/80 backdrop-blur-sm"
        >
            <div
                class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="flex size-8 items-center justify-center rounded-md bg-primary text-primary-foreground"
                    >
                        <HeartPulse class="size-5" />
                    </div>
                    <span class="text-lg font-semibold">Clínica Mediteam</span>
                </div>

                <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
                    <a href="#servicios" class="text-muted-foreground hover:text-foreground">Servicios</a>
                    <a href="#nosotros" class="text-muted-foreground hover:text-foreground">Nosotros</a>
                    <a href="#equipo" class="text-muted-foreground hover:text-foreground">Equipo médico</a>
                    <a href="#contacto" class="text-muted-foreground hover:text-foreground">Contacto</a>
                </nav>

                <div class="flex items-center gap-3">
                    <Button v-if="$page.props.auth.user" as-child>
                        <Link :href="dashboard()">Ir al panel</Link>
                    </Button>
                    <template v-else>
                        <Button variant="ghost" as-child>
                            <Link :href="login()">Iniciar sesión</Link>
                        </Button>
                        <Button as-child>
                            <Link :href="Citasindex()">Agendar cita</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </header>

        <main>
            <!-- Hero -->
            <section class="border-b bg-muted/30">
                <div class="mx-auto grid max-w-6xl items-center gap-12 px-6 py-16 sm:py-24 lg:grid-cols-2">
                    <div>
                        
                        <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                            Clínica Mediteam
                        </h1>
                        <p class="mt-6 text-lg text-muted-foreground">
                            Atención médica cercana y de confianza. Contamos
                            con consultas generales, especialistas renales y
                            personal de enfermería listos para cuidar de ti y
                            tu familia.
                        </p>
                        <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                            <Button size="lg" as-child>
                                <Link :href="Citasindex()">
                                    Agendar una cita
                                    <ArrowRight class="size-4" />
                                </Link>
                            </Button>
                            <Button size="lg" variant="outline" as-child>
                                <a href="#servicios">Ver servicios</a>
                            </Button>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border bg-muted shadow-sm">
                        <img
                            src="images/clinica/hero.jpg"
                            alt="Fachada de Clínica Mediteam"
                            class="aspect-4/3 w-full object-cover"
                        />
                    </div>
                </div>
            </section>

            <!-- Servicios -->
            <section id="servicios" class="py-20">
                <div class="mx-auto max-w-6xl px-6">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-3xl font-bold tracking-tight">
                            Nuestros servicios
                        </h2>
                        <p class="mt-4 text-muted-foreground">
                            En Clínica Mediteam ofrecemos atención integral, desde
                            la consulta general hasta el cuidado especializado.
                        </p>
                    </div>

                    <div class="mt-14 grid gap-6 sm:grid-cols-3">
                        <Card v-for="servicio in servicios" :key="servicio.titulo">
                            <CardHeader>
                                <div
                                    class="mb-2 flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                                >
                                    <component :is="servicio.icon" class="size-5" />
                                </div>
                                <CardTitle>{{ servicio.titulo }}</CardTitle>
                                <CardDescription>{{ servicio.descripcion }}</CardDescription>
                            </CardHeader>
                        </Card>
                    </div>
                </div>
            </section>

            <!-- Nosotros -->
            <section id="nosotros" class="border-t bg-muted/30 py-20">
                <div class="mx-auto grid max-w-6xl gap-12 px-6 lg:grid-cols-2 lg:items-center">
                   
                    <div class="overflow-hidden rounded-2xl border bg-muted shadow-sm lg:order-1">
                        <img
                            src="images/clinica/nosotros.jpg"
                            alt="Equipo de Clínica Mediteam"
                            class="aspect-4/3 w-full object-cover"
                        />
                    </div>

                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">
                            Sobre Clínica Mediteam
                        </h2>
                        <p class="mt-4 text-muted-foreground">
                            Somos una clínica comprometida con el bienestar de
                            nuestros pacientes. Combinamos experiencia médica,
                            trato humano y tecnología para que agendar una
                            cita, seguir un tratamiento o consultar tu
                            historial sea simple y rápido.
                        </p>
                        <ul class="mt-8 space-y-4 text-sm">
                            <li class="flex items-start gap-3">
                                <Clock class="mt-0.5 size-5 shrink-0 text-primary" />
                                <span>Atención de lunes a sábado, en horarios flexibles.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <CalendarCheck class="mt-0.5 size-5 shrink-0 text-primary" />
                                <span>Citas en línea, sin filas ni llamadas.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <HeartPulse class="mt-0.5 size-5 shrink-0 text-primary" />
                                <span>Seguimiento cercano de cada tratamiento.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Equipo médico -->
            <section id="equipo" class="py-20">
                <div class="mx-auto max-w-6xl px-6">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-3xl font-bold tracking-tight">
                            Nuestro equipo médico
                        </h2>
                        <p class="mt-4 text-muted-foreground">
                            Profesionales con experiencia dedicados al cuidado
                            de tu salud.
                        </p>
                    </div>

                    
                    <div class="mt-14 grid gap-6 sm:grid-cols-3">
                        <Card v-for="doctor in doctores" :key="doctor.nombre" class="overflow-hidden">
                            <div class="aspect-square w-full bg-muted">
                                <img
                                    :src="doctor.foto"
                                    :alt="doctor.nombre"
                                    class="size-full object-cover"
                                />
                            </div>
                            <CardHeader>
                                <CardTitle>{{ doctor.nombre }}</CardTitle>
                                <CardDescription>{{ doctor.especialidad }}</CardDescription>
                            </CardHeader>
                        </Card>
                    </div>
                </div>
            </section>

            <!-- Contacto -->
            <section id="contacto" class="border-t bg-muted/30 py-20">
                <div class="mx-auto max-w-6xl px-6">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-3xl font-bold tracking-tight">
                            Visítanos o agenda tu cita
                        </h2>
                        <p class="mt-4 text-muted-foreground">
                            Estamos para atenderte. Contáctanos por cualquiera
                            de estos medios.
                        </p>
                    </div>

                    <div class="mx-auto mt-14 grid max-w-3xl gap-6 sm:grid-cols-3">
                        <Card>
                            <CardContent class="flex flex-col items-center gap-2 text-center">
                                <MapPin class="size-6 text-primary" />
                                <p class="font-medium">Dirección</p>
                                <p class="text-sm text-muted-foreground">
                                    Av. Arce N.º 2519, La Paz, Bolivia
                                </p>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent class="flex flex-col items-center gap-2 text-center">
                                <Phone class="size-6 text-primary" />
                                <p class="font-medium">Teléfono</p>
                                <p class="text-sm text-muted-foreground">+591 2 2345678</p>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent class="flex flex-col items-center gap-2 text-center">
                                <Mail class="size-6 text-primary" />
                                <p class="font-medium">Correo</p>
                                <p class="text-sm text-muted-foreground">mediteamDev@mediteam.bo</p>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="mt-10 flex justify-center">
                        <Button size="lg" as-child>
                            <Link :href="Citasindex()">
                                Agendar una cita
                                <ArrowRight class="size-4" />
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t py-10">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 text-sm text-muted-foreground sm:flex-row">
                <div class="flex items-center gap-2">
                    <div
                        class="flex size-6 items-center justify-center rounded-md bg-primary text-primary-foreground"
                    >
                        <HeartPulse class="size-3.5" />
                    </div>
                    <span>Clínica Mediteam</span>
                </div>
                <p>© {{ new Date().getFullYear() }} Clínica Mediteam. Todos los derechos reservados.</p>
            </div>
            <p v-if="visitas !== null" class="mt-4 text-center text-xs text-muted-foreground">
                Esta página ha sido visitada {{ visitas }} {{ visitas === 1 ? 'vez' : 'veces' }}.
            </p>
        </footer>
    </div>
</template>
