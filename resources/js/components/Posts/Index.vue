<template>
    <div class="overflow-hidden overflow-x-auto p-6 bg-white border-gray-200">
        <div class="min-w-full align-middle">
            <div class="mb-4">
                <select v-model="selectedCategory" class="block mt-1 w-full sm:w-1/4 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" selected>-- Filter by category --</option>
                    <option v-for="category in uniqueDivisions">
                        {{ category }}
                    </option>
                </select>
            </div>
            <table id="myTable" class="min-w-full divide-y divide-gray-200 border">
                <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left">
                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"></span>
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left">
                        <div class="flex flex-row items-center justify-between cursor-pointer" @click="updateOrdering('no')">
                            <div class="leading-4 font-medium text-gray-500 up[percase tracking-wider" :class="{ 'font-bold text-blue-600': orderColumn === 'no' }">
                                ID
                            </div>
                            <div class="select-none">
                                <span :class="{
                                  'text-blue-600': orderDirection === 'asc' && orderColumn === 'no',
                                  'hidden': orderDirection !== '' && orderDirection !== 'asc' && orderColumn === 'no',
                                }">&uarr;</span>
                                <span :class="{
                                  'text-blue-600': orderDirection === 'desc' && orderColumn === 'no',
                                  'hidden': orderDirection !== '' && orderDirection !== 'desc' && orderColumn === 'no',
                                }">&darr;</span>
                            </div>
                        </div>
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left">
                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">DIVISION</span>
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left">
                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Content</span>
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left">
                        <div class="flex flex-row items-center justify-between cursor-pointer" @click="updateOrdering('inspection_date')">
                            <div class="leading-4 font-medium text-gray-500 uppercase tracking-wider" :class="{ 'font-bold text-blue-600': orderColumn === 'inspection_date' }">
                                Created at
                            </div>
                            <div class="select-none">
                                <span :class="{
                                  'text-blue-600': orderDirection === 'asc' && orderColumn === 'inspection_date',
                                  'hidden': orderDirection !== '' && orderDirection !== 'asc' && orderColumn === 'inspection_date',
                                }">&uarr;</span>
                                <span :class="{
                                  'text-blue-600': orderDirection === 'desc' && orderColumn === 'inspection_date',
                                  'hidden': orderDirection !== '' && orderDirection !== 'desc' && orderColumn === 'inspection_date',
                                }">&darr;</span>
                            </div>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                    
                    <tr v-for="(post, index) in posts.data" :key="post.id">
                        
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            <button v-if="!post.hideButton" @click="toggleAccordion(index)" class="flex w-full items-center justify-between text-left">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                                    class="h-6 w-6 transform duration-300" 
                                    :class="{'rotate-180': open[index]}">
                                    >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                </svg>                               
                            </button>
                        </td>

                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ post.no }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ post.division }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ post.content }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ post.inspection_date }}
                        </td>
                        
                    </tr>


                </tbody>
            </table>
            
            <TailwindPagination :data="posts" @pagination-change-page="page=> getPosts(page, selectedCategory)" class="mt-4"/>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch  } from "vue";
import usePosts from "../../composables/posts";
import useCategories from "../../composables/categories";
import useHistories from "../../composables/histories";

import { TailwindPagination } from 'laravel-vue-pagination';

const selectedCategory = ref('')
const orderColumn = ref('inspection_date')
const orderDirection = ref('desc')
const open = reactive({})

let lastIndex = null
let previousIndex = null
let previousFilteredData = null
let openedIndex = null
let previousNoHistoriesRow = null

const { posts, getPosts } = usePosts()
const { histories, getHistories } = useHistories()
const { categories, uniqueDivisions, getCategories } = useCategories()

onMounted(() => {
    getPosts()
    getCategories()
    getHistories()
})

const toggleAccordion = (index) => {
  const clickedItem = posts.value.data[index];
  const isOpen = open[index];

  if (Object.values(open).some(val => val)) {
    const openIndex = Object.keys(open).find(key => open[key]);
    if (openIndex && openIndex !== index) {
      open[openIndex] = false;
      const removeIndexes = [];
      posts.value.data.forEach((item, idx) => {
        if (item.hideButton) {
          removeIndexes.unshift(idx);
        }
      });
      removeIndexes.forEach(idx => {
        posts.value.data.splice(idx, 1);
      });
    }
  }

  open[index] = !isOpen;

  if (!isOpen) {
    let filteredData = histories.value.data.filter(item => {
      return item.no === clickedItem.no && item.division === clickedItem.division;
    });

    const matchingIndex = filteredData.findIndex(item => {
      return item.no === clickedItem.no &&
        item.division === clickedItem.division &&
        item.inspection_date === clickedItem.inspection_date;
    });

    if (matchingIndex !== -1) {
      filteredData.splice(matchingIndex, 1);
    }

    filteredData = filteredData.sort((a, b) => {
      return new Date(b.inspection_date) - new Date(a.inspection_date);
    });

    const insertIndex = posts.value.data.findIndex(item => item === clickedItem);
    
    const newRows = filteredData.map(item => ({
      ...item,
      hideButton: true
    }));


    newRows.forEach((item, idx) => {
      posts.value.data.splice(insertIndex + 1 + idx, 0, item);
    });

    if (filteredData.length === 0) {
      const insertIndex = posts.value.data.findIndex(item => item === clickedItem);

      const newRow = {
        no: '',
        division: '',
        content: 'No histories found',
        inspection_date: '', 
        hideButton: true
      };
    
      posts.value.data.splice(insertIndex + 1, 0, newRow);
    }

    let openKeys = Object.keys(open);
    // console.log('openKeys', openKeys)
    let rotatedIndex = openKeys.findIndex(key => open[key]);
    // console.log('rotatedIndex', rotatedIndex)

    if (rotatedIndex !== -1) {
      if(rotatedIndex >= 1) {
        Object.keys(open).forEach((key) => {
          open[key] = false;
        });

        open[insertIndex] = true;
        rotatedIndex = insertIndex;
      } else {
        openKeys[rotatedIndex] = String(insertIndex + 1);
      }
    }
  } else {
    const removeIndexes = [];
    posts.value.data.forEach((item, idx) => {
      if (item.hideButton) {
        removeIndexes.unshift(idx);
      }
    });
    removeIndexes.forEach(idx => {
      posts.value.data.splice(idx, 1);
    });
  }
}

const updateOrdering = (column) => {
    orderColumn.value = column;
    orderDirection.value = (orderDirection.value === 'asc') ? 'desc' : 'asc';
    getPosts(1, selectedCategory.value, orderColumn.value, orderDirection.value);
}

watch(selectedCategory, (current, _previous) => {
    getPosts(1, current)
})

</script>
