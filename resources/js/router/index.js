import { createRouter, createWebHistory } from 'vue-router'

import ClientsIndex from '../components/clients/ClientsIndex.vue'
import ClientsForm from '../components/clients/ClientsForm.vue'
import UsersIndex from '../components/users/UsersIndex.vue'
import UpdateUsersForm from '../components/users/UpdateUsersForm.vue'
import CreateUsersForm from '../components/users/CreateUsersForm.vue'

const routes = [
    {
        path: '/dashboard',
        name: 'clients.index',
        component: ClientsIndex
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
    {
        path: '/users',
        name: 'users.index',
        component: UsersIndex
    },
    {
        path: '/users/create',
        name: 'users.create',
        component: CreateUsersForm
    },
    {
        path: '/users/:id/update',
        name: 'users.update',
        component: UpdateUsersForm,
        props: true
    },
];

export default createRouter({
    history: createWebHistory(),
    routes
})