import { createRouter, createWebHistory } from 'vue-router'

import CompaniesIndex from '../components/clients/ClientsIndex.vue'
import ClientsCreate from '../components/clients/ClientsCreate.vue'
import ClientsUpdate from '../components/clients/ClientsUpdate.vue'

const routes = [
    {
        path: '/dashboard',
        name: 'clients.index',
        component: CompaniesIndex
    },
    {
        path: '/clients/create',
        name: 'clients.create',
        component: ClientsCreate
    },
    {
        path: '/clients/:id/update',
        name: 'clients.update',
        component: ClientsUpdate,
        props: true
    },
];

export default createRouter({
    history: createWebHistory(),
    routes
})