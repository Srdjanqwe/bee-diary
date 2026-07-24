import './bootstrap';
import '../css/app.css';
import '@vuepic/vue-datepicker/dist/main.css'; 

import VueDatePicker from '@vuepic/vue-datepicker'; 

import { createApp, onMounted } from 'vue'
import router from './routes/index.js'
import useAuth from "./composables/auth";
import { abilitiesPlugin } from '@casl/vue';
import ability from './services/ability';

import VueSweetalert2 from 'vue-sweetalert2';

const app = createApp({
    setup() {
        const { getUser } = useAuth()
        onMounted(getUser)
    }
})

app.use(router)
app.use(VueSweetalert2)
app.use(abilitiesPlugin, ability)
app.component('VueDatePicker', VueDatePicker)
app.mount('#app')