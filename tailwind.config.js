const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Nunito", ...defaultTheme.fontFamily.sans],
            },
        },
    },
    daisyui: {
        themes: [
            {
                mytheme: {
                    primary: "#20E299",
                    secondary: "#66E8B6",
                    accent: "#E32B77",
                    neutral: "#1d1a2d",
                    "base-100": "#FFFFFF",
                    info: "#2BC4E3",
                    success: "#14E31F",
                    warning: "#facc15",
                    error: "#E34120",
                    colors: {
                        primary: {
                            50: "#f4fefa",
                            100: "#e9fcf5",
                            200: "#c7f8e6",
                            300: "#a6f3d6",
                            400: "#63ebb8",
                            500: "#20E299",
                            600: "#1dcb8a",
                            700: "#18aa73",
                            800: "#13885c",
                            900: "#106f4b",
                        },
                    },
                },
            },
        ],
    },
    plugins: [require("@tailwindcss/typography"), require("daisyui")],
};
