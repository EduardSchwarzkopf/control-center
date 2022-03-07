import { createRouter, createWebHistory } from 'vue-router'

import CompaniesIndex from '../components/clients/ClientsIndex.vue'
import ClientsForm from '../components/clients/ClientsForm.vue'

const routes = [
    {
        path: '/dashboard',
        name: 'clients.index',
        component: CompaniesIndex
    },
    {
        path: '/clients/create',
        name: 'clients.create',
        component: ClientsForm
    },
    {
        path: '/clients/:id/update',
        name: 'clients.update',
        component: ClientsForm,
        props: true
    },
];

export default createRouter({
    history: createWebHistory(),
    routes
})