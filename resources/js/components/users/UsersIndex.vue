<template>
    <div class="flex place-content-end mb-4">
        <div
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
            <router-link
                :to="{ name: 'users.create' }"
                class="text-sm font-medium"
                >Create user</router-link
            >
        </div>
    </div>

    <div
        class="overflow-hidden overflow-x-auto min-w-full align-middle sm:rounded-md"
    >
        <table class="min-w-full border divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50">
                        <span
                            class="text-xs font-medium tracking-wider leading-4 text-left text-gray-500 uppercase"
                        >
                            Name
                        </span>
                    </th>
                    <th class="px-6 py-3 bg-gray-50">
                        <span
                            class="text-xs font-medium tracking-wider leading-4 text-left text-gray-500 uppercase"
                        >
                            E-Mail
                        </span>
                    </th>
                    <th class="px-6 py-3 bg-gray-50">
                        <span
                            class="text-xs font-medium tracking-wider leading-4 text-left text-gray-500 uppercase"
                        >
                            Admin
                        </span>
                    </th>
                    <th class="bg-gray-50"></th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                <template v-for="item in users" :key="item.id">
                    <tr class="bg-white">
                        <td
                            class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap"
                        >
                            {{ item.name }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap"
                        >
                            {{ item.email }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap"
                        >
                            {{ item.is_admin }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-right leading-5 text-gray-900 whitespace-no-wrap"
                        >
                            <router-link
                                :to="{
                                    name: 'users.update',
                                    params: { id: item.id },
                                }"
                                class="mr-2 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Edit</router-link
                            >
                            <button
                                @click="deleteUser(item.id)"
                                v-if="item.id != currentUser.id"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import useUsers from "../../composables/users";
import { onMounted } from "vue";

export default {
    setup() {
        const {
            currentUser,
            users,
            isAdmin,
            getCurrentUser,
            getUsers,
            destroyUser,
        } = useUsers();

        const deleteUser = async (id) => {
            if (!window.confirm("You sure?")) {
                return;
            }

            await destroyUser(id);
            await getUsers();
        };

        onMounted(async () => {
            await getCurrentUser();
            isAdmin(currentUser);
            getUsers();
        });

        return {
            currentUser,
            users,
            deleteUser,
        };
    },
};
</script>
