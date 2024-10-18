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
                '6.5' : '1.66rem'
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
                    'DEFAULT': '#262c89',
                    '50': '#ecf3ff',
                    '100': '#dde8ff',
                    '200': '#c2d5ff',
                    '300': '#9db9ff',
                    '400': '#7691ff',
                    '500': '#556aff',
                    '600': '#373ff4',
                    '700': '#2a2fd8',
                    '800': '#252bae',
                    '900': '#262c89',
                    '950': '#141647',
                },
                'secondary': {
                    'DEFAULT': '#c9cefc',
                    '50': '#eff0fe',
                    '100': '#e1e5fe',
                    '200': '#c9cefc',
                    '300': '#a8aef9',
                    '400': '#8585f4',
                    '500': '#7268ec',
                    '600': '#634cdf',
                    '700': '#563dc5',
                    '800': '#46349f',
                    '900': '#363765',
                    '950': '#241d49',
                },
                'accent': {
                    'DEFAULT': '#6566C2',
                    '50': '#f2f5fb',
                    '100': '#e7ecf8',
                    '200': '#d3dbf2',
                    '300': '#b8c3e9',
                    '400': '#9aa6df',
                    '500': '#8189d3',
                    '600': '#6566c2',
                    '700': '#5757ab',
                    '800': '#48488b',
                    '900': '#40426f',
                    '950': '#252541',
                },
                'rose': {
                    'DEFAULT': '#f70279',
                    '50': '#fff0f9',
                    '100': '#ffe3f5',
                    '200': '#ffc7eb',
                    '300': '#ff99d9',
                    '400': '#ff59be',
                    '500': '#ff29a2',
                    '600': '#f70279',
                    '700': '#dd0061',
                    '800': '#b60050',
                    '900': '#970445',
                    '950': '#5e0025',
                },
                'red': {
                    'DEFAULT': '#ff0000',
                    '50': '#fff0f0',
                    '100': '#ffdddd',
                    '200': '#ffc0c0',
                    '300': '#ff9494',
                    '400': '#ff5757',
                    '500': '#ff2323',
                    '600': '#ff0000',
                    '700': '#d70000',
                    '800': '#b10303',
                    '900': '#920a0a',
                    '950': '#500000',
                },
                "gradient":{"start":"#9198e5", "end":"#1f0843"},
            }
        },
    },
    plugins: [
        plugin(function ({ addComponents, addVariant }) {
            addVariant('hover-focus', ['&:hover', '&:focus']);
            addComponents({
                '.icon': {
                  '@apply size-6 stroke-2 text-primary hover:text-primary-800 transition-colors duration-200':{},
                },
                '.icon-button': {
                  '@apply flex gap-2 w-fit font-bold text-primary hover:text-primary-800 text-percentage-105':{},
                }
            })
        }),
    ],
}
