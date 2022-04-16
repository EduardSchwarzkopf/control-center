<template>
    <div class="">
        <div class="md:grid md:grid-cols-3 md:gap-3">
            <div class="col-span-1 p-6 bg-white rounded">
                <div class="flex place-content-end mb-4">
                    <router-link
                        class="btn btn-primary"
                        :to="{ name: 'clients.create' }"
                        >Add client</router-link
                    >
                </div>
                <div class="space-y-2">
                    <div v-for="item in clients" :key="item.id">
                        <router-link
                            :to="{
                                name: 'client.show',
                                params: { id: item.id },
                            }"
                            @click="setClientId(item.id)"
                        >
                            <div
                                class="w-full hover:bg-gray-100 rounded px-2 py-4"
                                :class="`${
                                    item.is_active == false ? 'opacity-50' : ''
                                }
                                ${
                                    state.clientId == item.id
                                        ? 'bg-primary-100'
                                        : ''
                                }`"
                            >
                                {{ item.name }}
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <router-view :key="state.clientId" :id="state.clientId" />
            </div>
        </div>
    </div>
</template>

<script>
import useClients from "../../composables/clients";
import { reactive } from "vue";

export default {
    methods: {
        setClientId(id) {
            this.state.clientId = id;
        },
    },
    setup() {
        const { clients, getClients } = useClients();
        const clientPath = window.location.pathname.match(/\d+/);
        const clientId = clientPath == null ? clientPath : clientPath[0];
        const state = reactive({
            clientId: clientId,
        });
        getClients();
        return {
            clients,
            state,
        };
    },
};
</script>
