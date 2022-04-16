<template>
    <div v-if="client">
        <div class="mb-2">
            <div class="grid grid-cols-3 gap-2">
                <div class="col-span-2 space-x-3">
                    <h3 class="text-2xl mb-2">{{ client.name }}</h3>
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
                        @click="deleteClient(item.id)"
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
                                heartbeat.status ? 'bg-success' : 'bg-error'
                            "
                        ></div>
                    </div>
                </div>
                <div v-if="lastHeartbeat">
                    <div
                        :class="
                            'badge badge-lg text-white bg-' +
                            (lastHeartbeat.status ? 'success' : 'error')
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

        <div class="my-12"></div>

        <div
            v-if="client.options"
            class="flex space-x-3 shadow bg-white p-4 rounded"
        >
            <div v-if="client.options.diskspace_threshold">
                <h4>Diskusage usage</h4>
                <Chart
                    :size="{ width: 400, height: 200 }"
                    :data="data"
                    :margin="margin"
                    :direction="direction"
                    :axis="axis"
                >
                    <template #layers>
                        <Grid strokeDasharray="2,2" />
                        <Line :dataKeys="['name', 'pl']" type="monotone" />
                    </template>
                </Chart>
                <small
                    >Check every:
                    {{ client.options.check_interval }} Seconds</small
                >
            </div>

            <div v-if="client.options.inodes_threshold">
                <h4>Inodes Usage</h4>
                <Chart
                    :size="{ width: 400, height: 200 }"
                    :data="data"
                    :margin="margin"
                    :direction="direction"
                    :axis="axis"
                >
                    <template #layers>
                        <Grid strokeDasharray="2,2" />
                        <Line :dataKeys="['name', 'pl']" type="monotone" />
                    </template>
                </Chart>
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
import { ref } from "vue";
import { Chart, Grid, Line, Marker } from "vue3-charts";

export default {
    props: {
        id: {
            type: [String, Number],
        },
    },
    components: { Chart, Grid, Line, Marker },

    computed: {
        lastHeartbeat() {
            return this.heartbeats.slice(-1)[0];
        },
    },

    setup(props) {
        const { environments, getEnvironments } = useEnvironments();
        const { heartbeats, getHeartbeats } = useHeartbeats();
        getEnvironments();

        const { client, getClient } = useClients();
        const clientId =
            props.id == null
                ? window.location.pathname.match(/\d+/)[0]
                : props.id;
        getClient(clientId);
        getHeartbeats(clientId, "status", 30);

        const data = [
            { name: "Jan", pl: 1000, avg: 500, inc: 300 },
            { name: "Feb", pl: 2000, avg: 900, inc: 400 },
            { name: "Apr", pl: 400, avg: 400, inc: 500 },
            { name: "Mar", pl: 3100, avg: 1300, inc: 700 },
            { name: "May", pl: 200, avg: 100, inc: 200 },
            { name: "Jun", pl: 600, avg: 400, inc: 300 },
            { name: "Jul", pl: 500, avg: 90, inc: 100 },
        ];

        const direction = ref("horizontal");
        const margin = ref({
            left: 0,
            top: 20,
            right: 20,
            bottom: 0,
        });

        const axis = ref({
            primary: {
                type: "band",
            },
            secondary: {
                domain: ["dataMin", "dataMax + 100"],
                type: "linear",
                ticks: 8,
            },
        });

        return {
            heartbeats,
            data,
            margin,
            axis,
            direction,
            environments,
            client,
        };
    },
};
</script>
