<template>
    <div v-if="client">
        <div class="mb-2">
            <div class="grid grid-cols-3 gap-2">
                <div class="col-span-2 space-x-3">
                    <h3 class="text-2xl mb-2">
                        {{ client.name }}
                        <span v-if="client.is_active == false" class="badge"
                            >Paused</span
                        >
                    </h3>
                </div>
                <div class="text-right">
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
                        @click="deleteClient(client.id)"
                        class="btn btn-outline btn-error btn-sm"
                    >
                        Delete
                    </button>
                </div>
            </div>
            <h4 v-if="client.client_environment" class="inline mr-3 text-xl">
                {{ client.client_environment.label }}
            </h4>
            <a :href="client.url" class="link inline" target="_blank">{{
                client.url
            }}</a>
        </div>

        <div class="my-12"></div>

        <div class="p-4 bg-white shadow rounded">
            <h4>Status</h4>
            <div class="grid grid-cols-3 gap-12">
                <div class="flex justify-end space-x-1 col-span-2">
                    <div
                        class=""
                        v-for="heartbeat in heartbeats"
                        :key="heartbeat"
                    >
                        <div
                            class="py-3 px-1 rounded"
                            :class="
                                heartbeat.status ? 'bg-primary' : 'bg-error'
                            "
                        ></div>
                    </div>
                </div>
                <div v-if="lastHeartbeat">
                    <div
                        :class="
                            'badge badge-lg text-white bg-' +
                            (lastHeartbeat.status ? 'primary' : 'error')
                        "
                    >
                        {{ lastHeartbeat.status ? "Up" : "Down" }}
                    </div>
                </div>
                <div v-else>
                    <div class="badge">laoding...</div>
                </div>
                <small v-if="client.options"
                    >Check every:
                    {{ client.options.check_interval }} Seconds</small
                >
            </div>
        </div>
    </div>
    <div v-else>
        <div
            class="flex flex-row min-h-screen justify-center items-center text-center"
        >
            <div class="flex justify-center items-center">
                <button class="btn btn-square loading"></button>
            </div>
        </div>
    </div>
</template>

<script>
import useClients from "../../composables/clients";
import useEnvironments from "../../composables/environments";
import useHeartbeats from "../../composables/heartbeats";

export default {
    props: {
        id: {
            type: [String, Number],
        },
    },

    methods: {},

    computed: {
        lastHeartbeat() {
            return this.heartbeats.slice(-1)[0];
        },
    },

    setup(props) {
        const { environments, getEnvironments } = useEnvironments();
        const { heartbeats, getHeartbeats } = useHeartbeats();
        getEnvironments();

        const { client, getClient, destroyClient } = useClients();

        const deleteClient = async (id) => {
            if (!window.confirm("You sure?")) {
                return;
            }

            destroyClient(id);
        };

        const clientId =
            props.id == null
                ? window.location.pathname.match(/\d+/)[0]
                : props.id;
        getClient(clientId);
        getHeartbeats(clientId, "status", 30);

        return {
            heartbeats,
            environments,
            client,
            deleteClient,
        };
    },
};
</script>
