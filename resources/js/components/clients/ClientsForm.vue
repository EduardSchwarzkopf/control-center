<template>
    <div class="mt-2 mb-6 text-sm text-red-600" v-if="errors !== ''">
        {{ errors }}
    </div>

    <form class="space-y-6" v-on:submit.prevent="saveClient">
        <div class="space-y-4 rounded-md shadow-sm">
            <div>
                <label for="name">Name</label>
                <div class="mt-1">
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="input input-bordered w-full"
                        v-model="form.name"
                    />
                </div>
            </div>

            <div>
                <label for="url">URL</label>
                <div class="mt-1">
                    <input
                        type="text"
                        name="url"
                        id="url"
                        class="input input-bordered w-full"
                        v-model="form.url"
                    />
                </div>
            </div>

            <div>
                <label for="is_active">Active</label>
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
                <label for="status_code">Expected status code</label>
                <div class="mt-1">
                    <select
                        name="status_code"
                        id="status_code"
                        class="select select-bordered"
                        v-model="form.status_code"
                    >
                        <option
                            v-for="(status_code, key) in status_code_list"
                            :value="form.status_code"
                            v-bind:key="status_code"
                        >
                            {{ key }}
                        </option>
                    </select>
                </div>
            </div>

            <div>
                <label for="environment">Environment</label>
                <div class="mt-1">
                    <select
                        v-model="form.client_environment_id"
                        class="select select-bordered"
                    >
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
            <div v-if="form.options">
                <label for="check_interval">Check Interval</label>
                <div class="mt-1">
                    <label class="input-group">
                        <input
                            type="number"
                            name="check_interval"
                            id="check_interval"
                            class="input input-bordered w-full"
                            v-model="form.options.check_interval"
                        />
                        <span>sec.</span>
                    </label>
                </div>
            </div>

            <div class="divider mt-8"></div>

            <div
                tabindex="0"
                class="collapse collapse-arrow border border-base-300 bg-base-100 rounded-box"
            >
                <input type="checkbox" />
                <div class="collapse-title text-xl font-medium">
                    Advanced options
                </div>
                <div class="collapse-content">
                    <div v-if="form.options">
                        <div>
                            <label for="backup_interval">Backup interval</label>
                            <div class="mt-1">
                                <label class="input-group">
                                    <input
                                        type="number"
                                        name="backup_interval"
                                        id="backup_interval"
                                        class="input input-bordered w-full"
                                        v-model="form.options.backup_interval"
                                    />
                                    <span>hours</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="backup_database_enabled"
                                >Database backup enabled</label
                            >
                            <div class="mt-1">
                                <input
                                    type="checkbox"
                                    name="backup_database_enabled"
                                    id="backup_database_enabled"
                                    class="toggle toggle-primary"
                                    v-model="
                                        form.options.backup_database_enabled
                                    "
                                />
                            </div>
                        </div>

                        <div>
                            <label for="backup_database_max_age"
                                >Database backup max age</label
                            >
                            <div class="mt-1">
                                <label class="input-group">
                                    <input
                                        type="number"
                                        name="backup_database_max_age"
                                        id="backup_database_max_age"
                                        class="input input-bordered w-full"
                                        v-model="
                                            form.options.backup_database_max_age
                                        "
                                    />
                                    <span>hours</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="backup_database_amount"
                                >Database backup on this server</label
                            >
                            <div class="mt-1">
                                <input
                                    type="number"
                                    name="backup_database_amount"
                                    id="backup_database_amount"
                                    class="input input-bordered"
                                    v-model="
                                        form.options.backup_database_amount
                                    "
                                />
                            </div>
                        </div>

                        <div>
                            <label for="backup_database_amount_remote"
                                >Database backup on client</label
                            >
                            <div class="mt-1">
                                <input
                                    type="number"
                                    name="backup_database_amount_remote"
                                    id="backup_database_amount_remote"
                                    class="input input-bordered w-full"
                                    v-model="
                                        form.options
                                            .backup_database_amount_remote
                                    "
                                />
                            </div>
                        </div>

                        <div>
                            <label for="backup_files_enabled"
                                >File backup enabled</label
                            >
                            <div class="mt-1">
                                <input
                                    type="checkbox"
                                    name="backup_files_enabled"
                                    id="backup_files_enabled"
                                    class="toggle toggle-primary"
                                    v-model="form.options.backup_files_enabled"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="backup_files_max_age"
                                >File backup max age</label
                            >
                            <div class="mt-1">
                                <label class="input-group">
                                    <input
                                        type="number"
                                        name="backup_files_max_age"
                                        id="backup_files_max_age"
                                        class="input input-bordered w-full"
                                        v-model="
                                            form.options.backup_files_max_age
                                        "
                                    />
                                    <span>hours</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="backup_files_amount"
                                >File backup amount on this server</label
                            >
                            <div class="mt-1">
                                <input
                                    type="number"
                                    name="backup_files_amount"
                                    id="backup_files_amount"
                                    class="input input-bordered w-full"
                                    v-model="form.options.backup_files_amount"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="backup_files_amount_remote"
                                >File backup amount on client</label
                            >
                            <div class="mt-1">
                                <input
                                    type="number"
                                    name="backup_files_amount_remote"
                                    id="backup_files_amount_remote"
                                    class="input input-bordered w-full"
                                    v-model="
                                        form.options.backup_files_amount_remote
                                    "
                                />
                            </div>
                        </div>

                        <div>
                            <label for="diskspace_threshold"
                                >Diskspace threshold</label
                            >
                            <div class="mt-1">
                                <label class="input-group">
                                    <input
                                        type="number"
                                        name="diskspace_threshold"
                                        id="diskspace_threshold"
                                        class="input input-bordered w-full"
                                        v-model="
                                            form.options.diskspace_threshold
                                        "
                                    />
                                    <span>%</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="inodes_threshold"
                                >Inodes threshold</label
                            >
                            <div class="mt-1">
                                <label class="input-group">
                                    <input
                                        type="number"
                                        name="inodes_threshold"
                                        id="inodes_threshold"
                                        class="input input-bordered w-full"
                                        v-model="form.options.inodes_threshold"
                                    />
                                    <span>%</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</template>

<script>
import useClients from "../../composables/clients";
import useEnvironments from "../../composables/environments";
import { reactive } from "vue";

export default {
    props: {
        id: {
            type: [String, Number],
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

        const status_code_list = {
            "200 OK": 200,
            "201 Created": 201,
            "301 Moved Permanently": 301,
            "307 Temporary Redirect": 307,
            "401 Unauthorized": 401,
            "403 Forbidden": 403,
            "404 Not Found": 404,
        };

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

            getEnvironments();
            getClient(props.id);

            saveClient = async () => {
                await updateClient(props.id);
            };
        } else {
            getEnvironments();
        }

        return {
            errors,
            status_code_list,
            environments,
            exclude_fields,
            form,
            saveClient,
        };
    },
};
</script>
