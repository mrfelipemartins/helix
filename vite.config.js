/** @type {import('vite').UserConfig} */
export default {
    resolve: {
        alias: {
            jsnview: 'node_modules/jsnview',
        }
    },
    optimizeDeps: {
        exclude: ['jsnview']
    },
    build: {
        assetsDir: "",
        rollupOptions: {
            input: ["resources/js/helix.js", "resources/css/helix.css"],
            output: {
                assetFileNames: "[name][extname]",
                entryFileNames: "[name].js",
            },
        },
    },
};
