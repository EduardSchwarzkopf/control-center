import { createRouter, createWebHistory } from 'vue-router'

import CompaniesIndex from '../components/clients/ClientsIndex.vue'

const routes = [
    {
        path: '/dashboard',
        name: 'client.index',
        component: CompaniesIndex
    }
];

export default createRouter({
    history: createWebHistory(),
    routes
})