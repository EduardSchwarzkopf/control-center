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
                        v-model="user.name"
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
                        v-model="user.email"
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
                        v-bind="{ user }"
                        v-model="user.password"
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
                        v-model="user.password_confirm"
                        v-bind="{ user }"
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
                        v-model="user.is_admin"
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
import { onMounted } from "vue";

export default {
    props: {
        id: {
            required: true,
            type: String,
        },
    },

    setup(props) {
        const {
            errors,
            user,
            currentUser,
            getCurrentUser,
            updateUser,
            getUser,
            canEdit,
        } = useUsers();

        onMounted(async () => {
            await getCurrentUser();
            await canEdit(currentUser, props.id);

            getUser(props.id);
        });

        const saveUser = async () => {
            await updateUser(props.id);
        };

        return {
            errors,
            user,
            currentUser,
            saveUser,
        };
    },
};
</script>
