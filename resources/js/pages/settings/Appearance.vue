<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Baby,
    Briefcase,
    Check,
    Contrast,
    Rocket,
    SlidersHorizontal,
    Type,
} from '@lucide/vue';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { useThemeCustomization } from '@/composables/useThemeCustomization';
import { edit } from '@/routes/appearance';
import type {
    ColorScheme,
    FontFamily,
    FontSize,
    ThemeProfile,
} from '@/types';
import Switch from '@/components/ui/switch/Switch.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Configuración de apariencia',
                href: edit(),
            },
        ],
    },
});

const {
    themeProfile,
    fontFamily,
    fontSize,
    contrast,
    colorScheme,
    isCustom,
    updateThemeProfile,
    updateFontFamily,
    updateFontSize,
    updateContrast,
    updateColorScheme,
} = useThemeCustomization();

const profiles: {
    value: ThemeProfile;
    icon: typeof Baby;
    label: string;
    description: string;
}[] = [
    {
        value: 'ninos',
        icon: Baby,
        label: 'Niños',
        description: 'Letra grande y colores muy marcados. La barra lateral usa el color principal.',
    },
    {
        value: 'jovenes',
        icon: Rocket,
        label: 'Jóvenes',
        description: 'Letra pequeña y un contraste de colores ligero pero presente.',
    },
    {
        value: 'adultos',
        icon: Briefcase,
        label: 'Adultos',
        description: 'Letra mediana y colores formales y discretos.',
    },
    {
        value: 'personalizado',
        icon: SlidersHorizontal,
        label: 'Personalizado',
        description: 'Elige tú mismo la fuente, el tamaño y el color principal.',
    },
];

const fonts: { value: FontFamily; label: string; sample: string }[] = [
    { value: 'modern', label: 'Moderna', sample: 'Instrument Sans' },
    { value: 'rounded', label: 'Redondeada', sample: 'Baloo 2' },
    { value: 'classic', label: 'Clásica', sample: 'Lora' },
];

const sizes: { value: FontSize; label: string }[] = [
    { value: 'small', label: 'Pequeño' },
    { value: 'medium', label: 'Mediano' },
    { value: 'large', label: 'Grande' },
];

const colorSchemes: { value: ColorScheme; label: string; swatchClass: string }[] = [
    { value: 'teal', label: 'Verde azulado', swatchClass: 'bg-[hsl(187,100%,26%)]' },
    { value: 'indigo', label: 'Índigo', swatchClass: 'bg-[hsl(243,75%,59%)]' },
    { value: 'rose', label: 'Rosa', swatchClass: 'bg-[hsl(347,77%,50%)]' },
    { value: 'amber', label: 'Ámbar', swatchClass: 'bg-[hsl(38,92%,42%)]' },
    { value: 'emerald', label: 'Esmeralda', swatchClass: 'bg-[hsl(152,69%,31%)]' },
    { value: 'slate', label: 'Pizarra', swatchClass: 'bg-[hsl(215,25%,27%)]' },
];
</script>

<template>
    <Head title="Configuración de apariencia" />

    <h1 class="sr-only">Configuración de apariencia</h1>

    <div class="space-y-8">
        <Heading
            variant="small"
            title="Configuración de apariencia"
            description="Actualiza la apariencia de tu cuenta"
        />

        <div class="space-y-2">
            <Label class="text-sm font-medium">Modo</Label>
            <AppearanceTabs />
        </div>

        <div class="space-y-3">
            <Label class="text-sm font-medium">Perfil de tema</Label>
            <div class="grid gap-3 sm:grid-cols-2">
                <button
                    v-for="profile in profiles"
                    :key="profile.value"
                    type="button"
                    @click="updateThemeProfile(profile.value)"
                    class="relative flex items-start gap-3 rounded-lg border p-4 text-left transition-colors hover:bg-accent"
                    
                >
                    <div
                        class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary"
                    >
                        <component :is="profile.icon" class="size-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium">{{ profile.label }}</p>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            {{ profile.description }}
                        </p>
                    </div>
                    <Check
                        v-if="themeProfile === profile.value"
                        class="absolute top-3 right-3 size-4 text-primary"
                    />
                </button>
            </div>
        </div>

        <Card v-if="isCustom">
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <SlidersHorizontal class="size-4" />
                    Personalización
                </CardTitle>
                <CardDescription>
                    Ajusta la fuente, el tamaño de texto y el color principal a tu gusto.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="space-y-2">
                    <Label class="flex items-center gap-1.5 text-sm font-medium">
                        <Type class="size-4" />
                        Fuente
                    </Label>
                    <div class="grid gap-2 sm:grid-cols-3">
                        <button
                            v-for="font in fonts"
                            :key="font.value"
                            type="button"
                            @click="updateFontFamily(font.value)"
                            class="rounded-lg border p-3 text-left transition-colors hover:bg-accent"
                            :class="
                                fontFamily === font.value
                                    ? 'border-primary bg-accent'
                                    : 'border-border'
                            "
                        >
                            <p class="text-sm font-medium">{{ font.label }}</p>
                            <p class="text-xs text-muted-foreground">{{ font.sample }}</p>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label class="text-sm font-medium">Tamaño de texto</Label>
                    <div class="grid gap-2 sm:grid-cols-3">
                        <button
                            v-for="size in sizes"
                            :key="size.value"
                            type="button"
                            @click="updateFontSize(size.value)"
                            class="rounded-lg border p-3 text-center text-sm font-medium transition-colors hover:bg-accent"
                            :class="
                                fontSize === size.value
                                    ? 'border-primary bg-accent'
                                    : 'border-border'
                            "
                        >
                            {{ size.label }}
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label class="text-sm font-medium">Color principal</Label>
                    <div class="flex flex-wrap gap-3">
                        <button
                            v-for="scheme in colorSchemes"
                            :key="scheme.value"
                            type="button"
                            @click="updateColorScheme(scheme.value)"
                            :title="scheme.label"
                            class="flex size-10 items-center justify-center rounded-full ring-2 ring-offset-2 ring-offset-background transition-all"
                            :class="[
                                scheme.swatchClass,
                                colorScheme === scheme.value
                                    ? 'ring-foreground'
                                    : 'ring-transparent',
                            ]"
                        >
                            <Check
                                v-if="colorScheme === scheme.value"
                                class="size-4 text-white mix-blend-difference"
                            />
                        </button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Contrast class="size-4" />
                    Accesibilidad
                </CardTitle>
                <CardDescription>
                    Aumenta el contraste de colores para mejorar la legibilidad, sin importar el perfil de tema elegido.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="flex items-center justify-between">
                    <Label for="high-contrast" class="text-sm">Alto contraste</Label>
                    <Switch
                        id="high-contrast"
                        :model-value="contrast === 'high'"
                        @update:model-value="(value: boolean) => updateContrast(value ? 'high' : 'normal')"
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
