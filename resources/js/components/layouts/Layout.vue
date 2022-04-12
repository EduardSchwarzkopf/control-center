<template>
    <div v-if="state.isReady">
        <navigation :currentUser="currentUser" />
        <div id="app-layout">
            <div class="py-12">
                <div class="grid grid-cols-1 mx-auto sm:px-6 lg:px-8">
                    <div class="overflow-hidden sm:rounded-lg">
                        <div class="">
                            <router-view />
                        </div>
                    </div>
                </div>
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
import { onMounted, reactive } from "@vue/runtime-core";
import Navigation from "../global/Navigation.vue";
import useUsers from "../../composables/users";

export default {
    components: {
        Navigation,
    },

    setup() {
        let state = reactive({ isReady: false });
        const { currentUser, getCurrentUser } = useUsers();

        onMounted(async () => {
            await getCurrentUser();
            state.isReady = true;
        });

        return { state, currentUser };
    },
};
</script>
