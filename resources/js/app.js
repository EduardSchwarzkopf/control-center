require('./bootstrap');

require('alpinejs');

import { createApp } from 'vue';
import router from './router'

import ClientsIndex from './components/clients/ClientsIndex.vue';
import UsersIndex from './components/users/UsersIndex.vue';

createApp({
    components: {
        ClientsIndex,
        UsersIndex
    }
}).use(router).mount('#app')