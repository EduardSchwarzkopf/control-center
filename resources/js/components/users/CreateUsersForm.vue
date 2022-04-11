<template>
    <div class="mt-2 mb-6 text-sm text-red-600" v-if="errors !== ''">
        {{ errors }}
    </div>

    <form class="space-y-6" v-on:submit.prevent="saveUser">
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
                <label for="email">E-Mail</label>
                <div class="mt-1">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="input input-bordered w-full"
                        v-model="form.email"
                    />
                </div>
            </div>
            <div>
                <label for="password">New Password</label>
                <div class="mt-1">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="input input-bordered w-full"
                        v-model="form.password"
                    />
                </div>
            </div>
            <div>
                <label for="password_confirm">Password confirm</label>
                <div class="mt-1">
                    <input
                        type="password"
                        name="password_confirm"
                        id="password-confirm"
                        class="input input-bordered w-full"
                        v-model="form.password_confirm"
                    />
                </div>
            </div>
            <div v-if="currentUser.is_admin">
                <label for="admin">Admin</label>
                <div class="mt-1">
                    <input
                        type="checkbox"
                        name="is_admin"
                        id="is_admin"
                        class="toggle toggle-primary"
                        v-model="form.is_admin"
                    />
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <button
            type="button"
            class="btn btn-ghost ml-3"
            @click="$router.push('/users')"
        >
            Cancel
        </button>
    </form>
</template>

<script>
import useUsers from "../../composables/users";
import { onMounted, reactive } from "vue";

export default {
    setup() {
        let form = reactive({
            name: "",
            email: "",
            password: "",
            password_confirm: "",
            is_admin: false,
        });

        const { errors, currentUser, storeUser, getCurrentUser } = useUsers();

        const saveUser = async () => {
            await storeUser({ ...form });
        };

        onMounted(async () => {
            await getCurrentUser();
        });

        return {
            errors,
            currentUser,
            form,
            saveUser,
        };
    },
};
</script>
