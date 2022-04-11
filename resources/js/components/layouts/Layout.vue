<template>
    <div v-if="state.isReady">
        <navigation :currentUser="currentUser" />
        <div id="app-layout">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6 bg-white border-b border-gray-200">
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
