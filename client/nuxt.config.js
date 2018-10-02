require('dotenv').config()

const polyfills = [
  'Promise',
  'Object.assign',
  'Object.values',
  'Array.prototype.find',
  'Array.prototype.findIndex',
  'Array.prototype.includes',
  'String.prototype.includes',
  'String.prototype.startsWith',
  'String.prototype.endsWith'
]

module.exports = {
  // mode: 'spa',

  srcDir: __dirname,

  env: {
    apiUrl: process.env.APP_URL || 'http://api.laravel-nuxt.test',
    wsUrl: process.env.SIGNAL_SERVER_WS_URL || false,
    wsEnabled: process.env.SIGNAL_SERVER_ENABLED || false,
    appName: process.env.APP_NAME || 'Laravel-Nuxt',
    appLocale: process.env.APP_LOCALE || 'en',
    githubAuth: !!process.env.GITHUB_CLIENT_ID
  },

  head: {
    title: process.env.APP_NAME,
    titleTemplate: '%s - ' + process.env.APP_NAME,
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'Nuxt.js project' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ],
    script: [
      { src: `https://cdn.polyfill.io/v2/polyfill.min.js?features=${polyfills.join(',')}` }
    ]
  },

  loading: { color: '#007bff' },

  router: {
    middleware: ['locale', 'check-auth']
  },

  css: [
    { src: '~assets/sass/app.scss', lang: 'scss' },
    'vue-material/dist/vue-material.min.css'
  ],

  plugins: [
    '~components/global',
    '~plugins/i18n',
    '~plugins/vform',
    '~plugins/axios',
    '~plugins/fontawesome',
    // '~plugins/nuxt-client-init',
    { src: '~plugins/bootstrap', ssr: false }
  ],

  modules: [
    '@nuxtjs/router',
    '@nuxtjs/toast',
    '~/modules/spa',
    ['nuxt-vue-material', {
      theme: 'default'
    }],
    'nuxt-material-design-icons'
  ],

  toast: {
    duration: 5000
  },

  build: {
    extractCSS: true
  }
}
