<template>
  <div class="w-[14rem]">
    <Label v-if="props.label" class="text-sm ml-2 capitalize font-semibold">{{ props.label }}</Label>
    <Popover class="relative">
      <PopoverButton
        class="w-full flex justify-between items-center px-3 py-2 border-2 rounded-lg shadow-sm bg-white text-left text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <span>
          {{ selectedDate ? selectedDate.toLocaleString() : "Selecciona fecha y hora" }}
        </span>
        <ChevronDownIcon class="h-5 w-5 text-gray-400" />
      </PopoverButton>

      <Transition
        enter="transition ease-out duration-200"
        enter-from="opacity-0 translate-y-1"
        enter-to="opacity-100 translate-y-0"
        leave="transition ease-in duration-150"
        leave-from="opacity-100 translate-y-0"
        leave-to="opacity-0 translate-y-1"
      >
        <PopoverPanel
          class="absolute z-10 mt-2 w-full rounded-xl bg-white shadow-lg left-1 ring-black/5 p-4"
        >
          <!-- Calendario -->
          <div class="grid grid-cols-7 gap-2 text-center text-sm">
            <div
              v-for="d in daysOfWeek"
              :key="d"
              class="font-medium text-gray-500"
            >
              {{ d }}
            </div>

            <button
              v-for="day in daysInMonth"
              :key="day.toISOString()"
              class="p-2 rounded-lg hover:bg-blue-100"
              :class="{ 'bg-blue-500 text-white': isSelected(day) }"
              @click="selectDate(day)"
            >
              {{ day.getDate() }}
            </button>
          </div>

          <!-- Hora -->
          <div class="mt-4 flex items-center justify-between">
            <label class="text-sm text-gray-600">Hora:</label>
            <input
              type="time"
              v-model="time"
              class="border rounded-lg px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </PopoverPanel>
      </Transition>
    </Popover>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import {
  Popover,
  PopoverButton,
  PopoverPanel,
} from "@headlessui/vue";
import { Transition } from "vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
//
const props = defineProps<{
  label?: string;
}>();

// Estado
const selectedDate = ref<Date | null>(null);
const time = ref<string>("12:00");

// Configuración calendario
const today = new Date();
const currentMonth = today.getMonth();
const currentYear = today.getFullYear();

const daysOfWeek: string[] = ["D", "L", "M", "X", "J", "V", "S"];

// Generar días del mes actual
const daysInMonth: Date[] = [];
const lastDay = new Date(currentYear, currentMonth + 1, 0).getDate();
for (let i = 1; i <= lastDay; i++) {
  daysInMonth.push(new Date(currentYear, currentMonth, i));
}

// Métodos
function selectDate(day: Date): void {
  const [hours, minutes] = time.value.split(":").map(Number);
  const newDate = new Date(day);
  newDate.setHours(hours, minutes);
  selectedDate.value = newDate;
}

function isSelected(day: Date): boolean {
  return (
    !!selectedDate.value &&
    day.toDateString() === selectedDate.value.toDateString()
  );
}
</script>
