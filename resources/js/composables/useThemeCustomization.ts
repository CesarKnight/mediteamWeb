import type { ComputedRef, Ref } from 'vue';
import { computed, ref } from 'vue';
import type {
    ColorScheme,
    Contrast,
    FontFamily,
    FontSize,
    ThemeProfile,
} from '@/types';

export type { ColorScheme, Contrast, FontFamily, FontSize, ThemeProfile };

export type ThemeProfileDefaults = {
    fontFamily: FontFamily;
    fontSize: FontSize;
};

export const PROFILE_DEFAULTS: Record<
    Exclude<ThemeProfile, 'personalizado'>,
    ThemeProfileDefaults
> = {
    ninos: { fontFamily: 'rounded', fontSize: 'large' },
    jovenes: { fontFamily: 'modern', fontSize: 'small' },
    adultos: { fontFamily: 'classic', fontSize: 'medium' },
};

export type UseThemeCustomizationReturn = {
    themeProfile: Ref<ThemeProfile>;
    fontFamily: Ref<FontFamily>;
    fontSize: Ref<FontSize>;
    contrast: Ref<Contrast>;
    colorScheme: Ref<ColorScheme>;
    isCustom: ComputedRef<boolean>;
    updateThemeProfile: (value: ThemeProfile) => void;
    updateFontFamily: (value: FontFamily) => void;
    updateFontSize: (value: FontSize) => void;
    updateContrast: (value: Contrast) => void;
    updateColorScheme: (value: ColorScheme) => void;
};

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getStored = <T extends string>(key: string): T | null => {
    if (typeof window === 'undefined') {
        return null;
    }

    return localStorage.getItem(key) as T | null;
};

const themeProfile = ref<ThemeProfile>('adultos');
const fontFamily = ref<FontFamily>('classic');
const fontSize = ref<FontSize>('medium');
const contrast = ref<Contrast>('normal');
const colorScheme = ref<ColorScheme>('teal');

function applyToDocument(): void {
    if (typeof document === 'undefined') {
        return;
    }

    const root = document.documentElement;

    root.dataset.themeProfile = themeProfile.value;
    root.dataset.fontFamily = fontFamily.value;
    root.dataset.fontSize = fontSize.value;
    root.dataset.contrast = contrast.value;
    root.dataset.colorScheme = colorScheme.value;
}

function persist(): void {
    localStorage.setItem('theme_profile', themeProfile.value);
    localStorage.setItem('font_family', fontFamily.value);
    localStorage.setItem('font_size', fontSize.value);
    localStorage.setItem('contrast', contrast.value);
    localStorage.setItem('color_scheme', colorScheme.value);

    setCookie('theme_profile', themeProfile.value);
    setCookie('font_family', fontFamily.value);
    setCookie('font_size', fontSize.value);
    setCookie('contrast', contrast.value);
    setCookie('color_scheme', colorScheme.value);
}

export function initializeThemeCustomization(): void {
    if (typeof window === 'undefined') {
        return;
    }

    themeProfile.value = getStored<ThemeProfile>('theme_profile') ?? 'adultos';
    fontFamily.value = getStored<FontFamily>('font_family') ?? 'classic';
    fontSize.value = getStored<FontSize>('font_size') ?? 'medium';
    contrast.value = getStored<Contrast>('contrast') ?? 'normal';
    colorScheme.value = getStored<ColorScheme>('color_scheme') ?? 'teal';

    applyToDocument();
}

export function useThemeCustomization(): UseThemeCustomizationReturn {
    // Note: state is hydrated once from localStorage by
    // initializeThemeCustomization() in app.ts, before any page mounts. Don't
    // re-read localStorage on mount here — doing so previously caused the
    // profile selector to flash to the 'adultos' default on every full page
    // reload before snapping to the real stored value a tick later.

    const isCustom = computed(() => themeProfile.value === 'personalizado');

    function updateThemeProfile(value: ThemeProfile): void {
        themeProfile.value = value;

        if (value !== 'personalizado') {
            const defaults = PROFILE_DEFAULTS[value];
            fontFamily.value = defaults.fontFamily;
            fontSize.value = defaults.fontSize;
        }

        applyToDocument();
        persist();
    }

    function updateFontFamily(value: FontFamily): void {
        fontFamily.value = value;
        applyToDocument();
        persist();
    }

    function updateFontSize(value: FontSize): void {
        fontSize.value = value;
        applyToDocument();
        persist();
    }

    function updateContrast(value: Contrast): void {
        contrast.value = value;
        applyToDocument();
        persist();
    }

    function updateColorScheme(value: ColorScheme): void {
        colorScheme.value = value;
        applyToDocument();
        persist();
    }

    return {
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
    };
}
