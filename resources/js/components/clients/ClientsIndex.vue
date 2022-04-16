<template>
    <div class="">
        <div class="grid grid-cols-3 gap-3">
            <div class="col-span-1 p-6 bg-white rounded">
                <div class="flex place-content-end mb-4">
                    <router-link
                        class="btn btn-primary"
                        :to="{ name: 'clients.create' }"
                        >Add client</router-link
                    >
                </div>
                <div class="space-y-2">
                    <div
                        :class="
                            state.cliendId == item.id ? 'bg-primary-100' : ''
                        "
                        v-for="item in clients"
                        :key="item.id"
                    >
                        <router-link
                            :to="{
                                name: 'client.show',
                                params: { id: item.id },
                            }"
                            @click="setClientId(item.id)"
                        >
                            <div
                                class="w-full hover:bg-gray-100 rounded px-2 py-4"
                                :class="`${item.is_active ? 'opacity-50' : ''}`"
                            >
                                {{ item.name }}
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
            <div class=""><router-view :key="state.cliendId" /></div>
        </div>
    </div>
</template>

<script>
import useClients from "../../composables/clients";
import { reactive } from "vue";

export default {
    methods: {
        setClientId(id) {
            this.state.cliendId = id;
        },
    },
    setup() {
        const { clients, getClients } = useClients();
        const state = reactive({ cliendId: 0 });
        getClients();
        return {
            clients,
            state,
        };
    },
};
</script>
