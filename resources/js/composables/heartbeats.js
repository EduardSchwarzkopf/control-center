import { ref } from "vue";
import axios from "axios";

export default function useHeartbeats() {
    const heartbeats = ref([]);

    const getHeartbeats = async (clientId, type, amount = 30) => {
        let response = await axios.get(
            `/api/heartbeats/${clientId}/${type}?amount=${amount}`
        );
        heartbeats.value = response.data;
    };

    return {
        heartbeats,
        getHeartbeats,
    };
}
