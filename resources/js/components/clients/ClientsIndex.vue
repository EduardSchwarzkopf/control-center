<template>
    <div class="flex place-content-end mb-4">
        <div class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <router-link :to="{ name: 'clients.create' }" class="text-sm font-medium">Create client</router-link>
        </div>
    </div>

    <div class="overflow-hidden overflow-x-auto min-w-full align-middle sm:rounded-md">
       <table class="min-w-full border divide-y divide-gray-200">
            <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50">
                    <span class="text-xs font-medium tracking-wider leading-4 text-left text-gray-500 uppercase">
                        Name
                    </span>
                </th>
                <th class="px-6 py-3 bg-gray-50">
                    <span class="text-xs font-medium tracking-wider leading-4 text-left text-gray-500 uppercase">
                        URL
                    </span>
                </th>
                <th class="bg-gray-50"></th>
            </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
            <template v-for="item in clients" :key="item.id">
                <tr class="bg-white">
                    <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                        {{ item.name }}
                    </td>
                    <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                        {{ item.options.url }}
                    </td>
                    <td class="px-6 py-4 text-sm text-center leading-5 text-gray-900 whitespace-no-wrap">
                        <router-link :to="{ name: 'clients.update', params: { id: item.id } }"
                                class="mr-2 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Edit</router-link>
                        <button @click="deleteClient(item.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Delete</button>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import useClients from '../../composables/clients'
import { onMounted } from 'vue';

export default {
    setup() {
        const { clients, getClients, destroyClient } = useClients()

        const deleteClient = async (id) => {
            if (!window.confirm('You sure?')) {
                return
            }

            await destroyClient(id)
            await getClients()
        }

        onMounted(getClients)

        return {
            clients,
            deleteClient
        }
    }
}
</script>