<template>
    <div class="p-6 bg-white">

      <VueDatePicker
        v-model="date"
        inline auto-apply
        :enable-time-picker="false"
        :markers="markers"
      />
  
      <!-- Important Dates Section -->
      <div class="mt-6">
        <h3 class="text-sm font-medium text-gray-900 mb-2">Reminder Important Dates</h3>
        <ul class="space-y-1">
          <li v-for="(date, index) in importantDates" 
              :key="index" 
              class="text-xs text-gray-600">
            {{ date }}
          </li>
        </ul>
      </div>
    </div>
  </template>
  
<script setup>
import { ref, computed } from 'vue';
import { addDays, startOfMonth, getDay } from 'date-fns';
import { isLineBreak } from 'typescript';

  const date = ref(new Date());

  // Funkcija za generisanje markera treće nedelje u mesecu
  const getThirdWeekMarkers = (currentDate) => {
  const firstDayOfMonth = startOfMonth(currentDate);
  const daysInMonth = Array.from({ length: 31 }, (_, i) => addDays(firstDayOfMonth, i))
    .filter((day) => day.getMonth() === currentDate.getMonth()); // Ograničimo na trenutni mesec

  // Pronađemo prvi ponedeljak u mesecu
  const firstMondayIndex = daysInMonth.findIndex((day) => getDay(day) === 1);

  // Ako nema ponedeljka (npr. februar sa 28 dana), prekinemo
  if (firstMondayIndex === -1) return [];

  // Početak i kraj treće nedelje
  const startOfThirdWeek = firstMondayIndex + 14; // Treći ponedeljak
  const endOfThirdWeek = startOfThirdWeek + 7; // Kraj treće nedelje

  // Kreiramo markere za svaki dan treće nedelje
  return daysInMonth.slice(startOfThirdWeek, endOfThirdWeek).map((day) => ({
    date: day,
    type: 'line', 
    color: 'orange',
  }));
  };

  // Generišemo markere za trenutni mesec
  const markers = ref(getThirdWeekMarkers(new Date()));



  // const markers = ref([
  // {
  //   // date: addDays(new Date(), 3),
  //   type: 'line',
  //   color: 'orange',
  // },
// ])

  const importantDates = ref([
    '1st week of May',
    'Last week in August'
  ])

</script>