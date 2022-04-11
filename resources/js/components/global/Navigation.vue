<template>
    <div class="navbar bg-base-100">
        <div class="flex-1">
            <a class="normal-case text-xl" href="/">
                <Logo class="inline mr-2" />
                Control Center</a
            >
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal p-0 space-x-2">
                <li>
                    <router-link
                        :to="{
                            name: 'clients.index',
                        }"
                    >
                        Clients</router-link
                    >
                </li>
                <li v-if="currentUser.is_admin">
                    <router-link
                        :to="{
                            name: 'users.index',
                        }"
                    >
                        Users</router-link
                    >
                </li>
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost normal-case">
                        {{ currentUser.name }}
                    </label>
                    <ul
                        tabindex="0"
                        class="mt-3 p-2 shadow menu menu-compact dropdown-content bg-base-100 rounded-box w-52"
                    >
                        <li>
                            <router-link
                                :to="{
                                    name: 'users.update',
                                    params: { id: currentUser.id },
                                }"
                                class="w-full"
                            >
                                Settings</router-link
                            >
                        </li>
                        <li>
                            <a v-on:click="logout" class="w-full">Logout</a>
                        </li>
                    </ul>
                </div>
            </ul>
        </div>
    </div>
</template>

<script>
import Logo from "./LogoComponent.vue";
export default {
    components: { Logo },
    props: {
        currentUser: {
            type: Object,
        },
    },

    methods: {
        async logout() {
            await axios.post("/api/logout");
            window.location.reload();
        },
    },

    setup(props) {
        const currentUser = props.currentUser;

        console.log(currentUser);
        return { currentUser };
    },
};
</script>
