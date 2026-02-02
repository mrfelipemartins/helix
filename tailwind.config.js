const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./resources/views/**/*.blade.php"],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                'sans': ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    safelist: [
        // layout & spacing
        'relative',
        'absolute',
        'hidden',
        'italic',
        'select-none',
        'cursor-pointer',

        // lists
        'list-none',
        'pl-7',
        'm-0',

        // borders
        'border-l',
        'border-dotted',

        // positioning
        '-left-4',
        'top-1',

        // text sizes
        'text-xs',
        'text-sm',

        // transitions
        'transition-transform',
        'duration-100',
        'ease-in-out',

        // background / container
        'p-4',
        'rounded-lg',
        'shadow-sm',

        // 🔥 color patterns (THIS IS THE IMPORTANT PART)
        {
            pattern: /text-(slate|gray|amber|blue)-(100|200|300|400|500|600|700|800|900)/,
        },
        {
            pattern: /border-(slate|gray)-(100|200|300|400|500|600|700|800)/,
        },
        {
            pattern: /bg-(slate|gray)-(800|900)/,
        },
    ],

    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        require("@tailwindcss/container-queries")
    ],
};