import { ref } from 'vue'

export default function useCategories() {
    const categories = ref({})
    const uniqueDivisions = ref([]);

    const getCategories = async () => {
        axios.get('/api/categories')
            .then(response => {
                categories.value = response.data.data;
                const divisions = [...new Set(response.data.data.map(category => category.division))];
                    uniqueDivisions.value = divisions;
            })
    }

    return { categories, uniqueDivisions, getCategories }
}