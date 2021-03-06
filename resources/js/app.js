/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue';
import router from './router';
import App from './components/App';
import Login from './components/Login';

require('./bootstrap');

const app = new Vue({
    el: '#app',
    components: {
        App,
        Login
    },
    router
});
