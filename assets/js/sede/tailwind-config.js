/**
 * Tailwind CSS Configuration for SENA Academic System
 */
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "sena-green": "#39A900",
                "sena-orange": "#FC7323",
                "primary": "#39A900",
                "primary-dark": "#2d8500",
                "background-light": "#f6f7f8",
                "background-dark": "#101922",
                "surface-light": "#ffffff",
                "surface-dark": "#1a2632",
            },
            fontFamily: {
                "display": ["Public Sans", "sans-serif"]
            },
            borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
        },
    },
}
