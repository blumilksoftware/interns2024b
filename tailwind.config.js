module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontSize: {
                '512' : '32rem',
                'hovered-inf-btn' : '1.55rem'
            },
            height:{
                '88' : '22rem'
            },
            width: {
                '128': '32rem',
                '8xl': '98rem'
            },
            maxWidth: {
              '8xl': '98rem'
            },
            screens: {
                '2xs': '380px',
                'xs': '480px',
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
                'red':'#FF0000',
                "gradient":{"start":"#9198e5", "end":"#1f0843"},
            }
        },
    },
    plugins: [],
}
