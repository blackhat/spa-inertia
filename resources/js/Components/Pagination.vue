<!-- Pagination.vue -->
<script setup>
import { Link, router } from "@inertiajs/vue3"
import { computed, ref, watch } from "vue"

const props = defineProps({
  meta: { type: Object, required: true }, // Laravel paginator via Inertia
  radius: { type: Number, default: 2 },   // จำนวนหน้าที่โชว์รอบ ๆ current
  compactThreshold: { type: Number, default: 20 }, // <=20 ใช้ select, >20 ใช้ combobox
})

const current = computed(() => Number(props.meta.current_page ?? props.meta.currentPage ?? 1))
const last    = computed(() => Number(props.meta.last_page ?? props.meta.lastPage ?? 1))
const hasPages = computed(() => last.value > 1)

// ===== numbered window + ellipsis =====
const pages = computed(() => {
  const c = current.value, l = last.value, r = Math.max(1, props.radius)
  const set = new Set([1, l])
  for (let i = Math.max(1, c - r); i <= Math.min(l, c + r); i++) set.add(i)
  const arr = Array.from(set).sort((a,b)=>a-b)

  const out = []
  for (let i = 0; i < arr.length; i++) {
    const v = arr[i]
    if (i === 0) { out.push(v); continue }
    const prev = arr[i-1]
    if (v - prev === 2) out.push(prev + 1)
    else if (v - prev > 2) out.push("…")
    out.push(v)
  }
  return out
})

// ===== URL builder (preserve query) =====
function buildHref(pageNumber) {
  const n = Number(pageNumber)
  if (!Number.isFinite(n) || n < 1 || n > last.value) return ""
  const url = new URL(window.location.href)
  url.searchParams.set("page", String(n))
  return `${url.pathname}?${url.searchParams.toString()}`
}

const prevHref = computed(() => current.value > 1 ? buildHref(current.value - 1) : "")
const nextHref = computed(() => current.value < last.value ? buildHref(current.value + 1) : "")

// ===== Go to page (hybrid control) =====
const selected = ref(current.value)  // ใช้ทั้ง select และ combobox
watch(current, v => { selected.value = v })

function go(n) {
  const target = Number(n ?? selected.value)
  if (!Number.isInteger(target) || target < 1 || target > last.value) return
  const href = buildHref(target)
  // ใช้ Inertia router เพื่อ preserveScroll/state
  router.visit(href, { preserveScroll: true, preserveState: true })
}

// datalist สำหรับกรณี last ใหญ่มาก: แสดงครบถึง 500 หน้าแรก, จากนั้นทุก ๆ 10, และ 20 หน้าสุดท้าย
const datalistOptions = computed(() => {
  const l = last.value
  const out = []
  if (l <= 500) {
    for (let i = 1; i <= l; i++) out.push(i)
    return out
  }
  for (let i = 1; i <= 500; i++) out.push(i)
  for (let i = 510; i <= l - 20; i += 10) out.push(i)
  for (let i = Math.max(501, l - 19); i <= l; i++) out.push(i)
  return out
})
</script>

<template>
  <nav
    v-if="hasPages"
    class="flex flex-col items-start justify-between gap-3 px-4 py-3 md:flex-row md:items-center"
    role="navigation"
    aria-label="Pagination"
  >
    <!-- Summary -->
    <div class="text-sm text-gray-600">
      Showing
      <span class="font-semibold text-gray-800">{{ meta.from ?? 0 }}–{{ meta.to ?? 0 }}</span>
      of
      <span class="font-semibold text-gray-800">{{ meta.total ?? 0 }}</span>
      <span class="hidden md:inline"> (Page {{ current }} of {{ last }})</span>
    </div>

    <!-- Controls -->
    <div class="flex flex-col-reverse items-start w-full gap-3 md:flex-row md:items-center md:gap-4 md:w-auto">
      <!-- Page list -->
      <ul class="inline-flex -space-x-px overflow-hidden border border-gray-200 rounded-lg">
        <!-- First -->
        <li>
          <Link
            :href="current > 1 ? buildHref(1) : ''"
            :aria-disabled="current === 1"
            aria-label="First page"
            preserve-scroll
            class="inline-flex items-center px-3 text-sm border-gray-200 h-9 border-e"
            :class="current === 1 ? 'pointer-events-none text-gray-300' : 'hover:bg-gray-50 text-gray-700'"
          >
            « First
          </Link>
        </li>

        <!-- Prev -->
        <li>
          <Link
            :href="prevHref"
            :aria-disabled="!prevHref"
            aria-label="Previous page"
            preserve-scroll
            class="inline-flex items-center px-3 text-sm border-gray-200 h-9 border-e"
            :class="!prevHref ? 'pointer-events-none text-gray-300' : 'hover:bg-gray-50 text-gray-700'"
          >
            ‹ Prev
          </Link>
        </li>

        <!-- Numbered pages + ellipsis -->
        <li v-for="(p, i) in pages" :key="i">
          <span
            v-if="p === '…'"
            class="inline-flex items-center px-3 text-sm text-gray-500 border-gray-200 select-none h-9 border-e"
          >…</span>
          <Link
            v-else
            preserve-scroll
            :href="buildHref(p)"
            :aria-current="p === current ? 'page' : undefined"
            class="inline-flex items-center px-3 text-sm border-gray-200 h-9 border-e"
            :class="p === current ? 'bg-blue-600 text-white pointer-events-none' : 'text-gray-700 hover:bg-gray-50'"
          >
            {{ p }}
          </Link>
        </li>

        <!-- Next -->
        <li>
          <Link
            :href="nextHref"
            :aria-disabled="!nextHref"
            aria-label="Next page"
            preserve-scroll
            class="inline-flex items-center px-3 text-sm border-gray-200 h-9 border-e"
            :class="!nextHref ? 'pointer-events-none text-gray-300' : 'hover:bg-gray-50 text-gray-700'"
          >
            Next ›
          </Link>
        </li>

        <!-- Last -->
        <li>
          <Link
            :href="current < last ? buildHref(last) : ''"
            :aria-disabled="current === last"
            aria-label="Last page"
            preserve-scroll
            class="inline-flex items-center px-3 text-sm h-9"
            :class="current === last ? 'pointer-events-none text-gray-300' : 'hover:bg-gray-50 text-gray-700'"
          >
            Last »
          </Link>
        </li>
      </ul>

      <!-- Hybrid: select (<=20) หรือ combobox (>20) -->
      <div class="flex items-center gap-2" aria-label="Go to page">
        <label for="goto" class="text-sm text-gray-600">Go to</label>

        <!-- <=20 หน้า: ใช้ select -->
        <select
          v-if="last <= compactThreshold"
          id="goto"
          v-model.number="selected"
          class="px-2 text-sm border border-gray-300 rounded-md w-14 h-9 focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="go(selected)"
        >
          <option v-for="n in last" :key="n" :value="n">{{ n }}</option>
        </select>

        <!-- >20 หน้า: ใช้ combobox (พิมพ์ได้ + มี datalist) -->
        <div v-else class="flex items-center gap-2">
          <input
            id="goto"
            v-model.number="selected"
            list="pages-datalist"
            inputmode="numeric"
            pattern="[0-9]*"
            :placeholder="`1-${last}`"
            class="w-16 px-2 text-sm border border-gray-300 rounded-md h-9 focus:outline-none focus:ring-2 focus:ring-blue-500"
            aria-label="Go to page number"
            @keyup.enter="go(selected)"
          />
          <datalist id="pages-datalist">
            <option v-for="n in datalistOptions" :key="n" :value="n">{{ n }}</option>
          </datalist>
          <button
            type="button"
            class="px-3 text-sm text-gray-700 border border-gray-300 rounded-md h-9 hover:bg-gray-50"
            @click="go(selected)"
          >
            Go
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>
