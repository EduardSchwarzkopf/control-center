import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

export default function useSettings() {
    const settings = ref([])
    const setting = ref({})
    const errors = ref('')
    const router = useRouter()

    const getSettings = async () => {
        let response = await axios.get('/api/settings')
        settings.value = response.data
    }

    const getSetting = async (id) => {
        let response = await axios.get(`/api/settings/${id}`)
        setting.value = response.data
        console.log(setting.value)
    }

    const destroySetting = async (id) => {
        await axios.delete(`/api/settings/${id}`)
    }

    const storeSetting = async (data) => {
        errors.value = ''

        try {
            await axios.post('/api/settings', data)
            await router.push({ name: 'settings.index' })
        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value += e.response.data.errors[key][0] + ' ';
                }
            }
        }
    }

    const updateSetting = async (id) => {
        errors.value = ''

        try {
            await axios.patch(`/api/settings/${id}`, setting.value)
            await router.push({ name: 'settings.index' })
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
        setting,
        settings,
        getSetting,
        getSettings,
        storeSetting,
        updateSetting,
        destroySetting
    }
}