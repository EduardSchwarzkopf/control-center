<template>
    <div class="flex place-content-end mb-4">
        <router-link :to="{ name: 'users.create' }" class="btn btn-primary"
            >Create user</router-link
        >
    </div>

    <div class="overflow-x-auto">
        <table class="table table-zebra w-full text-center">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Admin</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in users" :key="item.id">
                    <td>
                        {{ item.id }}
                    </td>
                    <td>
                        {{ item.name }}
                    </td>
                    <td>
                        {{ item.email }}
                    </td>
                    <td>
                        {{ item.is_admin }}
                    </td>
                    <td>
                        <router-link
                            :to="{
                                name: 'users.update',
                                params: { id: item.id },
                            }"
                            class="mr-2 btn btn-sm"
                        >
                            Edit</router-link
                        >
                        <button
                            @click="deleteClient(item.id)"
                            v-if="item.id != currentUser.id"
                            class="btn btn-outline btn-error btn-sm"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
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
