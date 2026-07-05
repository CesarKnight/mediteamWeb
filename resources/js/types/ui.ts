export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';

export type AppVariant = 'header' | 'sidebar';

export type ThemeProfile = 'ninos' | 'jovenes' | 'adultos' | 'personalizado';
export type FontFamily = 'modern' | 'rounded' | 'classic';
export type FontSize = 'small' | 'medium' | 'large';
export type Contrast = 'normal' | 'high';
export type ColorScheme =
    | 'teal'
    | 'indigo'
    | 'rose'
    | 'amber'
    | 'emerald'
    | 'slate';

export type FlashToast = {
    type: 'success' | 'info' | 'warning' | 'error';
    message: string;
};
