const mix = require("laravel-mix");
const path = require("path");
const fs = require('fs');

// Theme directory path
const themeDir = path.resolve(__dirname, '../../../wp-content/themes/wpresidence');
mix.setPublicPath(themeDir);

const cssOutputDir = "public/css";
const jsOutputDir = "public/js";

// Suppress warnings
const suppressWarnings = false;

class SuppressWarningsPlugin {
    apply(compiler) {
        compiler.hooks.done.tap('SuppressWarningsPlugin', (stats) => {
            if (suppressWarnings) {
                stats.compilation.warnings = [];
            }
        });
    }
}

mix.webpackConfig({
    stats: suppressWarnings ? 'none' : 'normal',
    resolve: {
        fallback: {
            buffer: require.resolve("buffer/")
        }
    },
    optimization: {
        minimize: true,
        splitChunks: false, // Disable code splitting
    },
    performance: {
        hints: false,
    },
    devtool: 'source-map',
    plugins: [
        new SuppressWarningsPlugin()
    ]
});

mix.options({
    autoprefixer: { remove: false },
    processCssUrls: false,
    cssNano: {
        discardComments: { removeAll: true }
    },
    legacyNodePolyfills: true,
    terser: {
        extractComments: false
    },
    clearConsole: suppressWarnings,
}); 

if (suppressWarnings) {
    mix.sass = (function(sassFunction) {
        return function() {
            return sassFunction.apply(this, arguments).webpackConfig({
                stats: 'none'
            });
        };
    })(mix.sass);
}


// Compile LTR CSS
// Compile SCSS files into main.css, excluding dashboard/ folder
let scssFiles = fs.readdirSync(path.join(themeDir, 'scss'))
                  .filter(file => file.endsWith('.scss') && 
                  !file.startsWith('dashboard/') && 
                   file !== 'rtl.scss'
                  )
                  .map(file => path.join(themeDir, 'scss', file));

scssFiles.forEach(file => {
    mix.sass(file, path.join(cssOutputDir, 'main.css')).sourceMaps();
});

// Compile RTL CSS specifically
const rtlScssPath = path.join(themeDir, 'scss/rtl.scss');
if (fs.existsSync(rtlScssPath)) {
    mix.sass(rtlScssPath, path.join(cssOutputDir, 'rtl.css')).sourceMaps();
} else {
    console.error('rtl.scss source file not found');
}

// Compile all SCSS files in dashboard folder into dashboard.css
let dashboardScssPath = path.join(themeDir, 'scss/dashboard/dashboard.scss');

// Ensure dashboard.scss exists and compile it
if (fs.existsSync(dashboardScssPath)) {
    mix.sass(dashboardScssPath, path.join(cssOutputDir, 'dashboard.css')).sourceMaps();
} else {
    console.error('dashboard.scss source file not found');
}

// Add Bootstrap JS with the exact output name
mix.copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', `${jsOutputDir}/bootstrap.bundle.min.js`)
   .version();

// Add custom JS
const appJsPath = path.join(themeDir, 'js/app.js');
if (fs.existsSync(appJsPath)) {
    mix.js(appJsPath, `${jsOutputDir}/app.js`).sourceMaps();
} else {
    console.error('App.js source file not found');
}

mix.disableSuccessNotifications();

if (!suppressWarnings) {
    mix.webpackConfig({
        stats: {
            children: true
        }
    });
}
