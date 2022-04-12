<template>
    <div class="prose lg:prose-xl">
        <h3>{{ client.name }}</h3>
        <router-link
            :to="{
                name: 'clients.update',
                id: client.id,
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

        <div class="my-12"></div>

        <div class="grid grid-cols-3 gap-2">
            <div class="flex space-x-1 col-span-2">
                <div v-for="status in statusList" :key="status">
                    <div
                        class="py-3 px-1 rounded"
                        :class="status ? 'bg-success' : 'bg-error'"
                    ></div>
                </div>
            </div>
            <div
                :class="
                    'badge text-white badge-lg badge-' +
                    (status ? 'success' : 'error')
                "
            >
                {{ status ? "Up" : "Down" }}
            </div>
        </div>

        <div class="my-12"></div>

        // Cart goes here
    </div>
</template>

<script>
import useClients from "../../composables/clients";
import useEnvironments from "../../composables/environments";
import { onMounted } from "vue";

export default {
    props: {
        id: {
            type: String,
        },
    },

    setup(props) {
        const exclude_fields = [
            "id",
            "client_id",
            "created_at",
            "updated_at",
            "check_interval",
            "backup_interval",
        ];

        const { environments, getEnvironments } = useEnvironments();

        const { client, getClient } = useClients();

        onMounted(async () => {
            getEnvironments();
            getClient(props.id);
        });

        const statusList = [true, false, true, false];
        const status = statusList[statusList.length - 1];

        return {
            status,
            statusList,
            environments,
            client,
        };
    },
};
</script>
