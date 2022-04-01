<template>
    <div class="mt-2 mb-6 text-sm text-red-600" v-if="errors !== ''">
        {{ errors }}
    </div>

    <form class="space-y-6" v-on:submit.prevent="saveClient">
        <div class="space-y-4 rounded-md shadow-sm">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <div class="mt-1">
                    <input type="text" name="name" id="name"
                           class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           v-model="form.name">
                </div>
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                <div class="mt-1">
                    <input type="text" name="url" id="url"
                           class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           v-model="form.url">
                </div>
            </div>
            
            <div>
                <label for="environment" class="block text-sm font-medium text-gray-700">Environment</label>
                    <div class="mt-1">
                        <select v-model="form.client_environment_id">
                            <option v-for="(env, index) in environments" :value="env.id" v-bind:key="index">
                                {{ env.label}}
                            </option>
                        </select>
                    </div>
            </div>

        </div>

        <button type="submit"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-gray-800 rounded-md border border-transparent ring-gray-300 transition duration-150 ease-in-out hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring disabled:opacity-25">
            Save
        </button>
    </form>
</template>

<script>
import useClients from '../../composables/clients'
import useEnvironments from '../../composables/environments'
import { onMounted, reactive} from 'vue';

export default {
    props: {
        id: {
            type: String
        }
    },

    setup(props) {
        
        let form = reactive({
            name: '',
            options: {
                url: '',
            },
        })

        const { errors, storeClient } = useClients()

        const {environments, getEnvironments} = useEnvironments()

        let saveClient = async () => {
            await storeClient({ ...form })
        }

        if (props.id) {
            const { client, updateClient, getClient } = useClients()
            form = client
            onMounted(() => {
                getEnvironments();
                getClient(props.id)}
                )
            
            saveClient = async () => {
                await updateClient(props.id)
            }
        } else {
            onMounted(() => getEnvironments());
        }

        return {
            errors,
            environments,
            form,
            saveClient
        }
    }
}
</script>