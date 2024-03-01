<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
} from 'chart.js';
import { Pie, Line } from 'vue-chartjs';
import { Calendar } from 'v-calendar';
import { Event } from '@/types/event';
import { computed } from 'vue';
import { subDays } from 'date-fns';
import { isBefore } from 'date-fns';
import { addDays } from 'date-fns';
import { endOfDay } from 'date-fns';
import { startOfDay } from 'date-fns';
import { isSameDay } from 'date-fns';
import { format } from 'date-fns';

ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
);

const props = defineProps<{
  student_count: number;
  this_month_consultation_count: number;
  this_month_event_count: number;
  consultations_per_category: {
    name: string;
    count: number;
  }[];
  consultations_per_day: {
    date: string;
    count: number;
  }[];
  events: (Event & {
    status: 'upcoming' | 'done';
  })[];
  most_active_students: {
    full_name: string;
    count: number;
  }[];
}>();

const upcomingEvents = computed(() =>
  props.events.filter((event) => event.status === 'upcoming'),
);

const eventCalendarAttributes = computed(
  (): InstanceType<typeof Calendar>['$props']['attributes'] => {
    const getRandomCalendarColor = () => {
      const colors = [
        'gray',
        'red',
        'orange',
        'yellow',
        'green',
        'teal',
        'blue',
        'indigo',
        'purple',
        'pink',
      ];

      return colors[Math.floor(Math.random() * (colors.length + 1))];
    };

    return props.events.map((event) => {
      return {
        key: event.id,
        dot: {
          color: getRandomCalendarColor(),
          class: event.status === 'done' ? 'opacity-50' : '',
        },
        popover: {
          label: `${event.title} @ ${event.event_time
            .split(':')
            .slice(0, 2)
            .join(':')}`,
        },
        dates: [new Date(event.event_date)],
      };
    });
  },
);

const getRandomColor = () => {
  return (
    'hsl(' +
    360 * Math.random() +
    ',' +
    (25 + 70 * Math.random()) +
    '%,' +
    (85 + 10 * Math.random()) +
    '%)'
  );
};

const consultationsPerCategoryData = computed(() => {
  return {
    labels: props.consultations_per_category.map((category) => category.name),
    datasets: [
      {
        backgroundColor: props.consultations_per_category.map(() =>
          getRandomColor(),
        ),
        data: props.consultations_per_category.map(
          (category) => category.count,
        ),
      },
    ],
  };
});

const consultationsPerDayData = computed(() => {
  const dates: {
    date: Date;
    count: number;
  }[] = [];

  const endDate = endOfDay(new Date());
  const startDate = startOfDay(subDays(endDate, 29));

  let indexToCheck = 0;

  for (let i = startDate; isBefore(i, endDate); i = addDays(i, 1)) {
    const datum = props.consultations_per_day.at(indexToCheck);

    if (datum !== undefined && isSameDay(i, new Date(datum.date))) {
      indexToCheck++;

      dates.push({
        date: i,
        count: datum.count,
      });

      continue;
    }

    dates.push({
      date: i,
      count: 0,
    });
  }

  return {
    labels: dates.map((date) => format(date.date, 'd MMM')),
    datasets: [
      {
        label: '',
        backgroundColor: getRandomColor(),
        data: dates.map((date) => date.count),
      },
    ],
  };
});
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white shadow-md sm:rounded-lg relative min-h-[calc(95vh-200px)] overflow-x-auto flex flex-col p-6"
        >
          <div
            class="mb-8 grid grid-cols-[repeat(auto-fit,minmax(240px,1fr))] gap-4"
          >
            <div class="bg-pink-100 text-pink-700 p-6 rounded-lg">
              <h3 class="text-5xl font-semibold">{{ student_count }}</h3>
              <p class="font-medium mt-1">total murid</p>
            </div>
            <div class="bg-blue-100 text-blue-700 p-6 rounded-lg">
              <h3 class="text-5xl font-semibold">
                {{ this_month_consultation_count }}
              </h3>
              <p class="font-medium mt-1">konsultasi di bulan ini</p>
            </div>
            <div class="bg-green-100 text-green-700 p-6 rounded-lg">
              <h3 class="text-5xl font-semibold">
                {{ this_month_event_count }}
              </h3>
              <p class="font-medium mt-1">kegiatan di bulan ini</p>
            </div>
          </div>

          <div
            class="grid grid-cols-[repeat(auto-fit,minmax(240px,1fr))] gap-4 mb-8"
          >
            <Calendar
              locale="id"
              expanded
              :attributes="[
                {
                  key: 'today',
                  highlight: true,
                  dates: [new Date()],
                },
                ...eventCalendarAttributes,
              ]"
            />

            <div class="flex flex-col items-center gap-4">
              <h3 class="text-2xl font-semibold">Konsultasi per Kategori</h3>
              <Pie
                class="max-h-[200px]"
                :data="consultationsPerCategoryData"
                :options="{
                  responsive: true,
                  maintainAspectRatio: false,
                }"
              />
            </div>

            <div class="flex flex-col items-center gap-4">
              <h3 class="text-2xl font-semibold">Konsultasi per Hari</h3>
              <Line
                class="max-h-[200px]"
                :data="consultationsPerDayData"
                :options="{
                  responsive: true,
                  maintainAspectRatio: false,
                  scales: {
                    y: {
                      ticks: {
                        precision: 0,
                      },
                      min: 0,
                    },
                  },
                }"
              />
            </div>
          </div>

          <div
            class="grid grid-cols-[repeat(auto-fit,minmax(240px,1fr))] gap-4"
          >
            <div class="flex flex-col gap-2">
              <h3 class="text-2xl font-semibold">Murid Paling Aktif</h3>
              <ul class="flex flex-col">
                <li
                  v-for="student in most_active_students"
                  :key="`${student.full_name}-${student.count}`"
                  class="bg-white shadow-sm rounded-md mb-2 px-4 py-2"
                >
                  <h4 class="font-medium">{{ student.full_name }}</h4>
                  <p class="text-gray-600 text-sm">
                    {{ student.count }} konsultasi
                  </p>
                </li>

                <li
                  v-if="most_active_students.length === 0"
                  class="bg-white border border-gray-100 text-center rounded-md mb-2 px-4 py-8"
                >
                  <p>Tidak terdapat konsultasi.</p>
                </li>
              </ul>
            </div>

            <div class="flex flex-col gap-2">
              <h3 class="text-2xl font-semibold">Kegiatan Mendatang</h3>
              <ul class="flex flex-col">
                <li
                  v-for="event in upcomingEvents"
                  :key="event.id"
                  class="bg-white shadow-sm rounded-md mb-2 px-4 py-2"
                >
                  <h4 class="font-medium">{{ event.title }}</h4>
                  <p class="text-gray-600 text-sm">
                    {{ format(new Date(event.event_date), 'd MMM y') }}
                    {{ event.event_time.split(':').slice(0, 2).join(':') }}
                  </p>
                </li>

                <li
                  v-if="upcomingEvents.length === 0"
                  class="bg-white border border-gray-100 text-center rounded-md mb-2 px-4 py-8"
                >
                  <p>Tidak terdapat kegiatan mendatang.</p>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
