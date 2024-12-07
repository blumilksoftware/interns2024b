import blumilkDefault from '@blumilksoftware/eslint-config'

const custom = {
  rules: {
    'function-multiline-arguments': {
      meta: {
        type: 'layout',
        fixable: 'code',
        schema: []
      },
      create: context => ({
        CallExpression: node => {
          if (node.arguments.length <= 1) return
          let startLine = node.callee.loc.end.line
          let endLine = node.loc.end.line
          if (startLine === endLine) return
          let argLines = node.arguments
            .flatMap(
              node => node.loc.start.line === node.loc.end.line
                ? [node.loc.start.line]
                : [node.loc.start.line, node.loc.end.line]
            )
          let endsInFunction = !!node.arguments[node.arguments.length - 1].body
          if (new Set(argLines.slice(0, -1)).size === 1 && endsInFunction) return
          argLines.push(startLine)
          argLines.push(endLine)
          if (new Set(argLines).size === argLines.length) return
          context.report({
            node,
            message: 'Enforce line breaks for multi-line function arguments',
            fix(fixer) {
              let actions = []
              node.arguments.forEach(
                (arg, i, arr) => {
                  let prev = i === 0 ? startLine : arr[i - 1].loc.end.line
                  if (arg.loc.start.line === prev) {
                    actions.push(fixer.insertTextBefore(arg, '\n'))
                  }
                }
              )
              let lastArg = node.arguments[node.arguments.length - 1]
              if (lastArg.loc.start.line === endLine) {
                actions.push(fixer.insertTextAfter(lastArg, '\n'))
              }
              return actions
            }
          })
        }
      })
    }
  }
}

export default [
  ...blumilkDefault,
  {
    plugins: {
      'custom': custom,
    },
    rules: {
      'quotes': 'off',
      'indent': 'off',
      'semi': 'off',
      'comma-dangle': 'off',
      'eol-last': 'off',
      '@typescript-eslint/no-var-requires': 'off',
      '@typescript-eslint/prefer-ts-expect-error': 'off',
      '@typescript-eslint/member-delimiter-style': 'off',

      'custom/function-multiline-arguments': 'warn',

      'no-console': 'warn',
      'no-empty': 'warn',
      'arrow-body-style': ['warn', 'as-needed'],

      'quotes': ['warn', 'single'],
      'indent': ['warn', 2],
      'semi': ['warn', 'never'],
      'comma-dangle': ['warn', 'always-multiline'],
      'eol-last': ['warn', 'always'],
      'no-multiple-empty-lines': ['warn', { max: 2, maxBOF: 0, maxEOF: 0 }],
      '@typescript-eslint/member-delimiter-style': ['warn', {
        multiline: { delimiter: 'none' },
        singleline: { delimiter: 'comma', requireLast: false },
      }],
      'comma-spacing': ['warn', { 'after': true }],
      'quote-props': ['warn', 'as-needed'],
      'rest-spread-spacing': ['warn', 'never'],
      'array-bracket-spacing': ['warn', 'never'],
      'array-bracket-newline': ['warn', { multiline: true }],
      'array-element-newline': ['warn', 'consistent'],
      'object-curly-spacing': ['warn', 'always'],
      'object-curly-newline': ['warn', { multiline: true }],
      'object-property-newline': ['warn', { allowMultiplePropertiesPerLine: true }],
      'key-spacing': 'warn',
      '@typescript-eslint/type-annotation-spacing': 'warn',
      'switch-colon-spacing': 'warn',
      'implicit-arrow-linebreak': ['warn', 'beside'],
      'arrow-spacing': 'warn',
      'brace-style': ['warn', 'stroustrup'],
      'function-paren-newline': ['warn', 'multiline-arguments'],
      'space-in-parens': ['warn', 'never'],
      'space-unary-ops': 'warn',

      '@typescript-eslint/no-require-imports': 'error',
      '@typescript-eslint/consistent-type-exports': ['warn', {
        fixMixedExportsWithInlineTypeSpecifier: true,
      }],
      '@typescript-eslint/consistent-type-imports': ['warn', {
        prefer: 'type-imports',
        disallowTypeAnnotations: true,
        fixStyle: 'inline-type-imports',
      }],
      '@typescript-eslint/method-signature-style': 'warn',
      '@typescript-eslint/naming-convention': ['warn', {
        selector: 'variableLike',
        leadingUnderscore: 'allow',
        trailingUnderscore: 'allow',
        format: ['camelCase', 'PascalCase', 'UPPER_CASE'],
      }],
      '@typescript-eslint/no-base-to-string': 'warn',
      '@typescript-eslint/no-dynamic-delete': 'warn',
      '@typescript-eslint/no-implied-eval': 'warn',
      '@typescript-eslint/no-namespace': 'warn',
      '@typescript-eslint/prefer-includes': 'warn',
      '@typescript-eslint/prefer-readonly': 'warn',
      '@typescript-eslint/promise-function-async': 'warn',
      '@typescript-eslint/consistent-type-assertions': ['warn', {
        assertionStyle: 'as',
        objectLiteralTypeAssertions: 'never',
      }],
      '@typescript-eslint/no-unused-vars': 'warn',
      '@typescript-eslint/no-inferrable-types': 'warn',

      'vue/max-attributes-per-line': ['warn', {
        'singleline': 1,
        'multiline': { 'max': 1 }
      }],
      'vue/singleline-html-element-content-newline': ['warn', { ignores: [] }],
      'vue/multiline-html-element-content-newline': ['warn', { ignores: [] }],
      'vue/first-attribute-linebreak': ['warn', {
        'singleline': 'beside',
        'multiline': 'below'
      }],
      'vue/block-tag-newline': ['warn', { 'maxEmptyLines': 0 }],
      'vue/padding-line-between-blocks': 'warn',
      'vue/padding-line-between-tags': 'warn'
    }
  }
]
