/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require("./bootstrap");
window.Vue = require("vue");

//Vue-Router
import VueRouter from "vue-router";
import { routes } from "./config/router/routes";
import LoadScript from "vue-plugin-load-script";
import 'animate.css'
import '@syncfusion/ej2-base/styles/material.css'
/* import '@syncfusion/ej2-base/styles/material.css';
import '@syncfusion/ej2-buttons/styles/material.css';
import '@syncfusion/ej2-calendars/styles/material.css';
import '@syncfusion/ej2-dropdowns/styles/material.css';
import '@syncfusion/ej2-inputs/styles/material.css';
import '@syncfusion/ej2-navigations/styles/material.css';
import '@syncfusion/ej2-popups/styles/material.css';
import '@syncfusion/ej2-splitbuttons/styles/material.css';
import "@syncfusion/ej2-vue-grids/styles/material.css";
import "@syncfusion/ej2-popups/styles/material.css"; */
import "./material-202011192113407143-13473947/compatibility/material.css";
import "ant-design-vue/dist/antd.css";
import Antd from "ant-design-vue";
import VueIziToast from "vue-izitoast";
import "izitoast/dist/css/iziToast.css";
Vue.use(VueIziToast);
Vue.use(LoadScript);
Vue.use(VueRouter);
Vue.use(Antd);

const router = new VueRouter({
  routes,
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('app-component', require('./components/App').default);
// Vue.component('test-component', require('./components/modules/Test').default);
import App from "./components/App";
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
  el: "#app",
  router,
  render: (h) => h(App),
});