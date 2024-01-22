import { ref } from 'vue'
import { useRouter } from 'vue-router'

export default function usePosts() {
    const posts = ref({})
    const router = useRouter()
    const validationErrors = ref({})
    const isLoading = ref(false)

    const getPosts = async (
        page = 1, 
        category = '',
        order_column = 'inspection_date',
        order_direction = 'desc',
        ) => {
        axios.get('api/posts?page=' + page + 
            '&category=' + category +
            '&order_column' + order_column +
            '&order_direction' + order_direction)
            .then(response => {
                posts.value = response.data;
                if (order_column === 'inspection_date' && order_direction === 'asc') {
                    posts.value.data.sort((a, b) => {
                        const dateA = new Date(a.inspection_date);
                        const dateB = new Date(b.inspection_date);
                        return dateA - dateB;
                    })
                } else if (order_column === 'no') {
                    posts.value.data.sort((a, b) => {
                        return order_direction === 'asc' ? a.no - b.no : b.no - a.no;
                    });
                }
            })
        }

        const storePost = async (post) => {
            
            if (isLoading.value) return;

            isLoading.value = true
            validationErrors.value = {}

            axios.post('/api/posts', post)
                .then(response => {
                    router.push({ name: 'posts.index' })
                })
                .catch(error => {
                    if (error.response?.data) {
                        validationErrors.value = error.response.data.errors
                    }
                })
                .finally(() => isLoading.value = false)
        }

    
    return { posts, getPosts, storePost, validationErrors, isLoading }
}