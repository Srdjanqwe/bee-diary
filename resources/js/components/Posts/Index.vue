<template>
      <div class="overflow-hidden overflow-x-auto p-6 bg-white border-gray-200">
          <div class="min-w-full align-middle flex space-x-6">
              <!-- <div class="mb-4">
                  <select v-model="selectedCategory" class="block mt-1 w-full sm:w-1/4 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                      <option value="" selected>-- Filter by category --</option>
                      <option v-for="category in uniqueDivisions">
                          {{ category }}
                      </option>
                  </select>
              </div> -->

              <!-- GLOBAL SEARCH -->
              <div class="flex-1">
                <div class="mb-4 grid lg:grid-cols-4 gap-4">
                  <input v-model="search_global" type="text" placeholder="Search" class="inline-block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                <!-- SEARCH by YEARS -->
                  <select v-model="selectedYear" class="inline-block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-500">
                      <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                    </select>
                </div>

                <!-- PDF dugme — između Search i filtera u headeru -->
                    <div class="mb-3">
                        <button
                        @click="downloadPdf"
                        :disabled="pdfLoading"
                        class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white text-xs font-semibold px-3 py-1.5 rounded transition-colors"
                        >
                        <!-- PDF ikona -->
                        <svg v-if="!pdfLoading" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <!-- Spinner -->
                        <svg v-else class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        {{ pdfLoading ? 'Generating...' : 'PDF' }}
                        </button>
                    </div>


                <table id="myTable" class="min-w-full divide-y divide-gray-200 border">
                    <thead>
                      <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left"></th>
                        <th class="px-6 py-3 bg-gray-50 text-left"></th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                          <select v-model="selectedCategory" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-500">
                            <option value="" selected>-- Filter by category --</option>
                            <option v-for="category in uniqueDivisions">
                              {{ category }}
                            </option>
                          </select>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left"></th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                          <select v-model="expired_days" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-500">
                            <option value="" selected>-- Not reviewed longer than --</option>
                              <option value="5">5 days</option>
                              <option value="7">7 days</option>
                          </select>
                        </th>
                      </tr>

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

                        <tr v-if="posts.data && posts.data.length === 0">
                          <td colspan="5" class="px-6 py-4 text-center text-sm leading-5 text-gray-500">
                            No transactions found
                          </td>
                        </tr>

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

                <!-- <TailwindPagination :data="posts" @pagination-change-page="page=> getPosts(page, selectedCategory)" class="mt-4"/> -->
                <TailwindPagination :data="posts" @pagination-change-page="handlePageChange" class="mt-4"/>
              </div>

            <!-- CaledarSidebar -->
            <!-- <div class="flex items-center justify-center bg-white border-gray-200"> -->
            <div class="sticky top-6 bg-white border-gray-200">
              <CalendarSidebar />
          </div>
        </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch, computed } from "vue";
import usePosts from "../../composables/posts";
import useCategories from "../../composables/categories";
import useHistories from "../../composables/histories";

import CalendarSidebar from './CalendarSidebar.vue';

import { TailwindPagination } from 'laravel-vue-pagination';

const selectedCategory = ref('')
const search_global = ref('')
const expired_days = ref('')
const selectedYear = ref(new Date().getFullYear())
const yearOptions = computed(() => {
  const currentYear = new Date().getFullYear()

  const years = (histories.value?.data || [])
    .filter(h => h.inspection_date) // odbaci null/prazne datume PRE parsiranja
    .map(h => new Date(h.inspection_date).getFullYear())
    .filter(y => !isNaN(y) && y > 2000 && y <= currentYear) // odbaci sve nerealne godine (npr. 1970)

  const uniqueYears = [...new Set(years)].sort((a, b) => b - a)

  if (!uniqueYears.includes(selectedYear.value)) {
    uniqueYears.unshift(selectedYear.value)
    uniqueYears.sort((a, b) => b - a)
  }

  return uniqueYears
})
const orderColumn = ref('inspection_date')
const orderDirection = ref('desc')
const open = reactive({})
const pdfLoading = ref(false)

let lastIndex = null
let previousIndex = null
let previousFilteredData = null
let openedIndex = null
let previousNoHistoriesRow = null

const { posts, getPosts } = usePosts()
const { histories, getHistories } = useHistories()
const { categories, uniqueDivisions, getCategories } = useCategories()

onMounted(() => {
  getPosts(1, selectedCategory.value, search_global.value, expired_days.value, orderColumn.value, orderDirection.value, selectedYear.value)
  getCategories()
  getHistories()


  // privremeno za debug
//   setTimeout(() => console.log('uniqueDivisions:', uniqueDivisions.value), 1000)

})

const downloadPdf = async () => {
  pdfLoading.value = true
  try {
    const response = await axios.get('/api/posts/pdf', {
      params: {
        search_global:   search_global.value,
        category:        selectedCategory.value,
        expired_days:    expired_days.value,
        order_column:    orderColumn.value,
        order_direction: orderDirection.value,
        year:            selectedYear.value,
      },
      responseType: 'blob'  // ključno - kaže axiоsu da očekuje binarni fajl
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url  = URL.createObjectURL(blob)
    window.open(url, '_blank')

    // Kreiraj link i klikni ga automatski
    const link = document.createElement('a')
    link.href = url
    link.download = 'posts-report.pdf'  // ← download umesto open
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)


    // Oslobodi memoriju nakon otvaranja
    setTimeout(() => URL.revokeObjectURL(url), 1000)

  } catch (error) {
    console.error('PDF error:', error)
    alert('Failed to generate PDF.')
  } finally {
    pdfLoading.value = false
  }
}

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
  resetSvgIcons();

  orderColumn.value = column;
  orderDirection.value = (orderDirection.value === 'asc') ? 'desc' : 'asc';
  getPosts(
    1,
    selectedCategory.value,
    search_global.value,
    expired_days.value,
    orderColumn.value,
    orderDirection.value,
    selectedYear.value
  );
}

const resetSvgIcons = () => {
// Resetuje sve otvorene ikone (accordion strelice)
Object.keys(open).forEach(key => {
  open[key] = false;
});
};

const handlePageChange = (page) => {
// Resetovanje otvorenih accordion stanja kada se promeni stranica
resetSvgIcons();

// Pozivanje funkcije koja dobavlja postove za novu stranicu sa trenutnom selektovanom kategorijom
getPosts(page, selectedCategory.value, search_global.value, expired_days.value, orderColumn.value, orderDirection.value, selectedYear.value);
};

watch(selectedCategory, (current, _previous) => {
  resetSvgIcons();
  getPosts(1, current, search_global.value, expired_days.value, orderColumn.value, orderDirection.value, selectedYear.value)
})

watch(search_global, (current, _previous) => {
  resetSvgIcons();
  getPosts(1, selectedCategory.value, current, expired_days.value, orderColumn.value, orderDirection.value, selectedYear.value)
})

watch(expired_days, (current, _previous) => {
  resetSvgIcons();
  getPosts(1, selectedCategory.value, search_global.value, current, orderColumn.value, orderDirection.value, selectedYear.value)
})
    
watch(selectedYear, (current, _previous) => {
  resetSvgIcons();
  getPosts(1, selectedCategory.value, search_global.value, expired_days.value, orderColumn.value, orderDirection.value, current)
})

</script>
