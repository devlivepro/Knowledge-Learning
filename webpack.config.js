const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Entry point for Sass file (style.scss)
    .addStyleEntry('style', './assets/css/style.scss')

    // Entry point for JavaScript file (app.js)
    .addEntry('app', './assets/app.js') // Fichier JS principal

    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')

    // Split files into smaller pieces for optimization
    .splitEntryChunks()

    // Enable single runtime to avoid code duplication
    .enableSingleRuntimeChunk()

    // Clean up the build directory before each build
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // Configure Babel pour les polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // Enable Sass/SCSS support
    .enableSassLoader()

    // Option if you use TypeScript
    //.enableTypeScriptLoader()

    // Option if you use React
    //.enableReactPreset()

    // Option if you have problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();