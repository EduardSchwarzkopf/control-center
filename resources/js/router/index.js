import { createRouter, createWebHistory } from "vue-router";

import ClientsIndex from "../components/clients/ClientsIndex.vue";
import ClientsForm from "../components/clients/ClientsForm.vue";
import ClientShow from "../components/clients/ClientShow.vue";
import UsersIndex from "../components/users/UsersIndex.vue";
import UpdateUsersForm from "../components/users/UpdateUsersForm.vue";
import CreateUsersForm from "../components/users/CreateUsersForm.vue";
import SettingsIndex from "../components/settings/SettingsIndex.vue";
import UpdateSettingsForm from "../components/settings/CreateSettingsForm.vue";
import CreateSettingsForm from "../components/settings/UpdateSettingsForm.vue";

import Layout from "../components/layouts/Layout.vue";

const routes = [
    {
        path: "/",
        name: "layout",
        component: Layout,
        children: [
            {
                path: "/dashboard",
                name: "clients.index",
                component: ClientsIndex,
                children: [
                    {
                        path: "/clients/create",
                        name: "clients.create",
                        component: ClientsForm,
                    },
                    {
                        path: "/clients/:id",
                        name: "client.show",
                        component: ClientShow,
                        props: true,
                    },
                    {
                        path: "/clients/:id/update",
                        name: "clients.update",
                        component: ClientsForm,
                        props: true,
                    },
                ],
            },
            {
                path: "/users",
                name: "users.index",
                component: UsersIndex,
            },
            {
                path: "/users/create",
                name: "users.create",
                component: CreateUsersForm,
            },
            {
                path: "/users/:id/update",
                name: "users.update",
                component: UpdateUsersForm,
                props: true,
            },
            {
                path: "/settings",
                name: "settings.index",
                component: SettingsIndex,
            },
            {
                path: "/settings/create",
                name: "settings.create",
                component: CreateSettingsForm,
            },
            {
                path: "/settings/:id/update",
                name: "settings.update",
                component: UpdateSettingsForm,
                props: true,
            },
        ],
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
