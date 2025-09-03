<script setup>
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  name:  { type: String, required: true },
  label: { type: String, required: true },
  query: { type: Object,  default: () => ({}) }, // 👈 รับ query เข้ามา
})

// ให้คลาสออกมา '', 'asc', 'desc' ตามค่าใน props.query
const sortClass = computed(() => {
  const s = props.query?.sort_by ?? ''
  const key = s.replace(/^-+/, '')
  if (key !== props.name) return ''
  return s.startsWith('-') ? 'desc' : 'asc'
})

function navigate() {
  // พกพารามิเตอร์เดิมทั้งหมด
  const params = { ...(props.query ?? {}) }

  const cur = params.sort_by ?? ''
  const curKey = cur.replace(/^-+/, '')
  const same = curKey === props.name

  // toggle เดิมคอลัมน์ → asc ↔ desc, เปลี่ยนคอลัมน์ → เริ่ม asc
  params.sort_by = same ? (cur.startsWith('-') ? props.name : `-${props.name}`) : props.name
  params.page = 1

  router.get(route(route().current()), params, {
    only: ['products','query'],
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}
</script>

<template>
  <a href="#" @click.prevent="navigate" class="sortable" :class="sortClass">
    {{ label }}
  </a>
</template>
