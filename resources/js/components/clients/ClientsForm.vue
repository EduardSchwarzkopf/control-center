<template>
    <div class="mt-2 mb-6 text-sm text-red-600" v-if="errors !== ''">
        {{ errors }}
    </div>

    <form class="space-y-6" v-on:submit.prevent="saveClient">
        <div class="space-y-4 rounded-md shadow-sm">
            <div>
                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700"
                    >Name</label
                >
                <div class="mt-1">
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        v-model="form.name"
                    />
                </div>
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-gray-700"
                    >URL</label
                >
                <div class="mt-1">
                    <input
                        type="text"
                        name="url"
                        id="url"
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        v-model="form.url"
                    />
                </div>
            </div>

            <div>
                <label
                    for="is_active"
                    class="block text-sm font-medium text-gray-700"
                    >Active</label
                >
                <div class="mt-1">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        class="toggle toggle-primary"
                        v-model="form.is_active"
                    />
                </div>
            </div>

            <div>
                <label
                    for="status_code"
                    class="block text-sm font-medium text-gray-700"
                    >Expected status code</label
                >
                <div class="mt-1">
                    <input
                        type="text"
                        name="status_code"
                        id="status_code"
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        v-model="form.status_code"
                    />
                </div>
            </div>

            <div>
                <label
                    for="environment"
                    class="block text-sm font-medium text-gray-700"
                    >Environment</label
                >
                <div class="mt-1">
                    <select v-model="form.client_environment_id">
                        <option
                            v-for="(env, index) in environments"
                            :value="env.id"
                            v-bind:key="index"
                        >
                            {{ env.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="">
                <h3 class="border-t-2 border-indigo-500 pt-2 mt-8">
                    Advanced options
                </h3>
            </div>

            <div v-if="form.options">
                <div>
                    <label
                        for="check_interval"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Check Interval (seconds)</label>
                    <div class="mt-1">
                        <input
                            type="number"
                            name="check_interval"
                            id="check_interval"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.check_interval"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_interval"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Backup interval (hours)</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="backup_interval"
                            id="backup_interval"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_interval"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_database_enabled"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Database backup enabled</label
                    >
                    <div class="mt-1">
                        <input
                            type="checkbox"
                            name="backup_database_enabled"
                            id="backup_database_enabled"
                            class="block mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_database_enabled"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_database_max_age"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Database backup max age (hours)</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="backup_database_max_age"
                            id="backup_database_max_age"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_database_max_age"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_database_amount"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Database backup on this server</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="backup_database_amount"
                            id="backup_database_amount"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_database_amount"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_database_amount_remote"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Database backup on client</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="backup_database_amount_remote"
                            id="backup_database_amount_remote"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_database_amount_remote"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_files_enabled"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >File backup enabled</label
                    >
                    <div class="mt-1">
                        <input
                            type="checkbox"
                            name="backup_files_enabled"
                            id="backup_files_enabled"
                            class="block mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_files_enabled"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_files_max_age"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >File backup max age (hours)</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="backup_files_max_age"
                            id="backup_files_max_age"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_files_max_age"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_files_amount"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >File backup amount on this server</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="backup_files_amount"
                            id="backup_files_amount"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_files_amount"
                        />
                    </div>
                </div>

                <div>
                    <label
                        for="backup_files_amount_remote"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >File backup amount on client</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="backup_files_amount_remote"
                            id="backup_files_amount_remote"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.backup_files_amount_remote"
                        />
                    </div>
                </div>


                <div>
                    <label
                        for="diskspace_threshold"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Diskspace threshold(percent)</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="diskspace_threshold"
                            id="diskspace_threshold"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.diskspace_threshold"
                        />
                    </div>
                </div>


                <div>
                    <label
                        for="inodes_threshold"
                        class="block first-letter:uppercase text-sm font-medium text-gray-700"
                        >Inodes threshold (percent)</label
                    >
                    <div class="mt-1">
                        <input
                            type="number"
                            name="inodes_threshold"
                            id="inodes_threshold"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            v-model="form.options.inodes_threshold"
                        />
                    </div>
                </div>

            </div>
        </div>

        <button
            type="submit"
            class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-gray-800 rounded-md border border-transparent ring-gray-300 transition duration-150 ease-in-out hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring disabled:opacity-25"
        >
            Save
        </button>

        <button 
        type="button"    
        class="inline-flex items-center mx-2 px-4 py-2 text-xs font-semibold tracking-widest  uppercase rounded-md border ring-gray-300 transition duration-150 ease-in-out"
        @click="$router.push('/dashboard')">
            Cancel
        </button>
    </form>
</template>

<script>
import useClients from "../../composables/clients";
import useEnvironments from "../../composables/environments";
import { onMounted, reactive } from "vue";

export default {
    props: {
        id: {
            type: String,
        },
    },

    setup(props) {
        let form = reactive({
            name: "",
            url: "",
            status_code: 200,
            client_environment_id: 1,
            is_active: true,
            options: {
                check_interval: 0,
                backup_interval: 0,
                backup_database_enabled: 0,
                backup_database_max_age: 0,
                backup_database_amount: 0,
                backup_database_amount_remote: 0,
                backup_files_enabled: 0,
                backup_files_max_age: 0,
                backup_files_amount: 0,
                backup_files_amount_remote: 0,
                diskspace_threshold: 0,
                inodes_threshold: 0,
            },
        });

        const exclude_fields = [
            "id",
            "client_id",
            "created_at",
            "updated_at",
            "check_interval",
            "backup_interval",
        ];

        const { errors, storeClient } = useClients();

        const { environments, getEnvironments } = useEnvironments();

        let saveClient = async () => {
            await storeClient({ ...form });
        };

        if (props.id) {
            const { client, updateClient, getClient } = useClients();
            form = client;

            onMounted(() => {
                getEnvironments();
                getClient(props.id);
            });

            saveClient = async () => {
                await updateClient(props.id);
            };
        } else {
            onMounted(() => getEnvironments());
        }

        return {
            errors,
            environments,
            exclude_fields,
            form,
            saveClient,
        };
    },
};
</script>
