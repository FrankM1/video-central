import globals from "globals";

export default [
    {
        languageOptions: {
            ecmaVersion: 2015,
            sourceType: "script",
            globals: {
                ...globals.browser,
                ...globals.node,
                ...globals.jquery,
                Backbone: "readonly",
                _: "readonly",
                jQuery: "readonly",
                wp: "readonly",
                video_central: "readonly"
            }
        },
        rules: {
            "eqeqeq": ["error", "smart"],
            "no-bitwise": "error",
            "new-cap": "error",
            "no-caller": "error",
            "curly": "error",
            "no-undef": "error",
            "no-trailing-spaces": "error",
            "no-new-wrappers": "error"
        }
    }
];
