import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

export default function useEnvironments() {
    const environments = ref([])
    const environment = ref({})
    const errors = ref('')
    const router = useRouter()

    const getEnvironments = async () => {
        let response = await axios.get('/api/environments')
        environments.value = response.data
    }

    const getEnvironment = async (id) => {
        let response = await axios.get(`/api/environments/${id}`)
        environment.value = response.data
    }

    const destroyEnvironment = async (id) => {
        await axios.delete(`/api/environments/${id}`)
    }

    const storeEnvironment = async (data) => {
        errors.value = ''

        try {
            await axios.post('/api/environments', data)
            await router.push({ name: 'environments.index' })
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value += e.response.data.errors[key][0] + ' ';
                }
            }
        }
    }

    const updateEnvironment = async (id) => {
        errors.value = ''

        try {
            await axios.patch(`/api/environments/${id}`, environment.value)
            await router.push({ name: 'environments.index' })
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value += e.response.data.errors[key][0] + ' ';
                }
            }
        }
    }

    return {
        errors,
        environment,
        environments,
        getEnvironment,
        getEnvironments,
        storeEnvironment,
        updateEnvironment,
        destroyEnvironment
    }
}