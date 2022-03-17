import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

export default function useUsers() {
    const currentUser = ref([])
    const users = ref([])
    const user = ref({})
    const errors = ref('')
    const router = useRouter()

    const getCurrentUser = async () => {

        let response = await axios.get('/api/users/current')
        currentUser.value = response.data
    }

    const canEdit = async (currentUser, userId) => {
        if (currentUser.value.is_admin || userId == currentUser.value.id) {
            return;
        }
        await router.push({ name: 'clients.index' })
    }

    const isAdmin = async (currentUser) => {
        if (currentUser.value.is_admin) {
            return;
        }
        await router.push({ name: 'clients.index' })
    }

    const getUsers = async () => {
        let response = await axios.get('/api/users')
        users.value = response.data
    }

    const getUser = async (id) => {
        let response = await axios.get(`/api/users/${id}`)
        user.value = response.data
        console.log(user.value)
    }

    const destroyUser = async (id) => {
        await axios.delete(`/api/users/${id}`)
    }

    const storeUser = async (data) => {
        errors.value = ''

        if (user.value.password !== user.value.password_confirmed) {
            errors.value = 'Passwords do not mach'
        } else {

            try {
                await axios.post('/api/users', data)
                await router.push({ name: 'users.index' })
            } catch (e) {
                if (e.response.status === 422) {
                    for (const key in e.response.data.errors) {
                        errors.value += e.response.data.errors[key][0] + ' ';
                    }
                }
            }
        }

    }

    const updateUser = async (id) => {
        errors.value = ''

        if (user.value.password !== user.value.password_confirm) {
            errors.value = 'Passwords do not mach'
        } else {

            try {
                await axios.patch(`/api/users/${id}`, user.value)
                await router.push({ name: 'users.index' })
            } catch (e) {
                if (e.response.status === 422) {
                    for (const key in e.response.data.errors) {
                        errors.value += e.response.data.errors[key][0] + ' ';
                    }
                }
            }
        }

    }

    return {
        errors,
        currentUser,
        user,
        users,
        canEdit,
        isAdmin,
        getUser,
        getCurrentUser,
        getUsers,
        storeUser,
        updateUser,
        destroyUser
    }
}