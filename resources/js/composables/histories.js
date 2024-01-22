import { ref } from 'vue'

export default function useHistories() {
    const histories = ref({})

    const getHistories = async () => {
        axios.get('/api/histories')
            .then(response => {
                histories.value = response.data;
            })
    }

    return { histories, getHistories }
}