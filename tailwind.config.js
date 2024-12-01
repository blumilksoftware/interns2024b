const plugin = require('tailwindcss/plugin')

module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            borderWidth: {
                '9' : '9px'
            },
            strokeWidth: {
                '1.5' : '1.5',
                '3' : '3',
                '4' : '4'
            },
            fontSize: {
                'md' : ['1rem', '1,5rem'],
                '512' : '32rem',
                'percentage-105' : '1.05rem'
            },
            height:{
                '7.5' : '1.875rem',
                '88' : '22rem'
            },
            width: {
                '7.5' : '1.875rem',
                '128': '32rem',
                '8xl': '98rem'
            },
            minHeight: {
                '6.5' : '1.66rem',
                '512' : '32rem',
                '616' : '38.5rem'
            },
            maxWidth: {
                '8xl': '98rem'
            },
            screens: {
                '2xs': '380px',
                'xs': '480px',
            },
            transitionProperty: {
                'colors-opacity' : {
                    'transition-property': 'color, background-color, border-color, text-decoration-color, fill, stroke, opacity'
                },
            },
            colors: {
                'primary': {
                    'DEFAULT': 'rgb(var(--color-primary))',
                    'bright': 'rgb(var(--color-primary-bright))',
                    'dark': 'rgb(var(--color-primary-dark))',
                },
                'red': {
                    'DEFAULT': '#ff0000',
                    '500': '#ff2323',
                },
                "gradient": {
                    "start":"#9198e5",
                    "end":"#1f0843"
                },
            }
        },
    },
    plugins: [
        plugin(function ({ addComponents, addVariant, addBase }) {
            addVariant('hover-focus', ['&:hover', '&:focus']);
            addBase({
              ':root': {
                '--color-primary': '228 0 125',
                '--color-primary-bright': '245 13 147',
                '--color-primary-dark': '176 4 96',
              },
            });
            addComponents({
                '.icon': {
                  '@apply size-6 stroke-2 text-primary hover:text-primary-bright transition-colors duration-200':{},
                },
                '.slide-up-animation' : {
                    '@apply hover:-translate-y-1 h-full transition-transform ease-in duration-150':{},
                },
                '.icon-button': {
                  '@apply flex gap-2 w-fit font-bold text-primary hover:text-primary-bright text-percentage-105':{},
                }
            })
        }),
    ],
}
