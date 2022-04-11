<template>
    <div class="flex place-content-end mb-4">
        <router-link class="btn btn-primary" :to="{ name: 'clients.create' }"
            >Create client</router-link
        >
    </div>

    <div class="overflow-x-auto">
        <table class="table table-zebra w-full text-center">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in clients" :key="item.id">
                    <td>
                        {{ item.id }}
                    </td>
                    <td>
                        {{ item.name }}
                    </td>
                    <td>
                        {{ item.url }}
                    </td>
                    <td>
                        {{ item.is_active }}
                    </td>
                    <td>
                        <router-link
                            :to="{
                                name: 'clients.update',
                                params: { id: item.id },
                            }"
                            class="mr-2 btn btn-sm"
                        >
                            Edit</router-link
                        >
                        <button
                            @click="deleteClient(item.id)"
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
import useClients from "../../composables/clients";
import { onMounted } from "vue";

export default {
    setup() {
        const { clients, getClients, destroyClient } = useClients();

        const deleteClient = async (id) => {
            if (!window.confirm("You sure?")) {
                return;
            }

            await destroyClient(id);
            await getClients();
        };

        onMounted(getClients);

        return {
            clients,
            deleteClient,
        };
    },
};
</script>
