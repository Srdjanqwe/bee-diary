<template>
    <form @submit.prevent="storePost(post)">
       <!-- Title -->
       <div>
           <label for="post-no" class="block text-sm font-medium text-gray-700">
               No.
           </label>
           <input  v-model="post.no" id="post-no" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
           <div class="text-red-600 mt-1">
               <div v-for="message in validationErrors?.no">
                   {{ message }}
               </div>
           </div>
       </div>

       <!-- Content -->
       <div class="mt-4">
           <label for="post-content" class="block text-sm font-medium text-gray-700">
               Content
           </label>
           <textarea v-model="post.content" id="post-content" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
           <div class="text-red-600 mt-1">
               <div v-for="message in validationErrors?.content">
                   {{ message }}
               </div>
           </div>
       </div>

       <!-- Category -->
       <div class="mt-4">
           <label for="post-category" class="block text-sm font-medium text-gray-700">
               Category
           </label>
           <select v-model="post.division" id="post-category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
               <option value="" selected>-- Choose category --</option>
                <option v-for="category in uniqueDivisions">
                        {{ category }}
                    </option>
           </select>


           <div class="text-red-600 mt-1">
               <div v-for="message in validationErrors?.division">
                   {{ message }}
               </div>
           </div>
       </div>

       <!-- Inspection date -->
       <div class="mt-4">
            <label for="post-inspection_date" class="block text-sm font-medium text-gray-700">
                Inspection date
            </label>
                <!-- <vPikaday v-model="post.inspection_date"
                >
                </vPikaday> -->

                <VueDatePicker v-model="post.inspection_date"
                    :format="formatDate"  
                    :enable-time-picker="false" 
                    :teleport="true">
                </VueDatePicker>
                
                
                <!-- <VueDatePicker v-model="inspectionDate" 
                    :format="formatDate" 
                    :enable-time-picker="false" 
                    :teleport="true">
                </VueDatePicker> -->
        
           <div class="text-red-600 mt-1">
               <div v-for="message in validationErrors?.inspection_date">
                   {{ message }}
               </div>
           </div>
       </div>

       <!-- Buttons -->
       <div class="mt-4">
           <button :disabled="isLoading" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm uppercase text-white disabled:opacity-75 disabled:cursor-not-allowed">
               <span v-show="isLoading" class="inline-block animate-spin w-4 h-4 mr-2 border-t-2 border-t-white border-r-2 border-r-white border-b-2 border-b-white border-l-2 border-l-blue-600 rounded-full"></span>
               <span v-if="isLoading">Processing</span>
               <span v-else>Save</span>
           </button>
       </div>
   </form>
</template>

<script>
import { onMounted, reactive, ref, watch } from 'vue';
import useCategories from '@/composables/categories';
import usePosts from '@/composables/posts';

import VueDatePicker from '@vuepic/vue-datepicker';

// import vPikaday from 'vue-pikaday';

export default {
    
    // components: {
    //     vPikaday,
    // },

    setup() {
        const post = reactive({
        no: '',
        content: '',
        division: '',
        inspection_date: '',
    });


    const { uniqueDivisions, getCategories } = useCategories();
    const { storePost, validationErrors, isLoading } = usePosts();

    const inspectionDate = ref('');

    const formatDate = (date) => {
      if (!date) return ''; 

      const formattedDate = new Date(date);
      const day = formattedDate.getDate();
      const month = formattedDate.getMonth() + 1;
      const year = formattedDate.getFullYear();

      return `${month}/${day}/${year}`;
    };

    watch(() => post.inspection_date, (newValue) => {
      inspectionDate.value = newValue;
    });


    onMounted(() => {
      getCategories();
    });

    return { 
      uniqueDivisions, 
      post, 
      inspectionDate,
      storePost, 
      validationErrors, 
      isLoading,
      formatDate,
    };
  },
};
</script>



