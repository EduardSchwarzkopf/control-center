import { createRouter, createWebHistory } from 'vue-router'

import CompaniesIndex from '../components/clients/ClientsIndex.vue'
import ClientsCreate from '../components/clients/ClientsCreate.vue'

const routes = [
    {
        path: '/dashboard',
        name: 'client.index',
        component: CompaniesIndex
    },
    {
        path: '/clients/create',
        name: 'clients.create',
        component: ClientsCreate
    },
];

export default createRouter({
    history: createWebHistory(),
    routes
})