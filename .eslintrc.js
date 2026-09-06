module.exports = {
  settings: {
    react: {
      version: 'detect'
    }
  },
  env: {
    browser: true,
    commonjs: true,
    es6: true,
    node: true,
  },
  extends: ["eslint:recommended", "plugin:react/recommended"],
  globals: {
    wp: "readonly",
    jQuery: "readonly",
    hpublic: "readonly",
    h5vpBlock: "readonly",
    h5vpAdmin: "readonly",
    h5vpI18n: "readonly",
    Plyr: "readonly",
    Hls: "readonly",
    dashjs: "readonly",
    argument: "readonly",
    debugger: "readonly",
    h5vpData: "readonly",
    ytpPlayer: "readonly",
    ReactDom: "readonly",
  },
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    ecmaVersion: 12,
    sourceType: "module",
  },
  plugins: ["react"],
  rules: {
    "func-names": "off",
    "no-console": "off",
    "no-unused-vars": ['warn', { 'argsIgnorePattern': '^__' }],
    "react/jsx-filename-extension": [1, { extensions: [".js", ".jsx"] }],
    "react/prop-types": "off",
    "react/react-in-jsx-scope": "off",
    "react/no-unknown-property": "off",
    "react/display-name": "off",
    "no-process-exit": "off",
    "no-unsafe-optional-chaining": "off",
    "object-shorthand": "warn",
    "class-methods-use-this": "off",
  },
};
