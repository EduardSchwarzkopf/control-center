<template>
    <div class="navbar bg-base-100">
        <div class="flex-1">
            <a class="normal-case text-xl" href="/">Control Center</a>
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
                        <li><a class="w-full">Logout</a></li>
                    </ul>
                </div>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        currentUser: {
            type: Object,
        },
    },

    setup(props) {
        const currentUser = props.currentUser;

        console.log(currentUser);
        return { currentUser };
    },
};
</script>
