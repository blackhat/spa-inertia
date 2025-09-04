<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { debounce } from 'lodash-es'
import Pagination from '@/Components/Pagination.vue' 
import Sortable from '@/Components/Sortable.vue'

const props = defineProps({
  products: { type: Object, required: true },
  // คุณส่งมาจาก controller: 'query' => request()->query()
  query: { type: Object, default: () => ({ search: '' }) }
})

const loading = ref(false)
const startedAt = ref(0)
const minVisibleMs = 400


// ✅ อินพุตคุมโดยโลคัลเสมอ
const search = ref(props.query?.search ?? '')

// ✅ เมื่อ response ใหม่เข้ามา (เปลี่ยนหน้า/โหลดซ้ำ) ค่อย sync ถ้าค่าไม่เท่ากัน
watch(() => props.query?.search, (v) => {
  if ((v ?? '') !== search.value) search.value = v ?? ''
})

// ค้นหา (รวม query เดิม + reset page)
const doSearch = debounce(() => {
  const url = new URL(window.location.href)
  const params = Object.fromEntries(url.searchParams.entries())
  const next = { ...params, search: search.value, page: 1 }

  router.get(route('products.index'), next, {
    only: ['products','query'],
    preserveState: true,
    preserveScroll: true,
    replace: true,
    onStart: () => {
      if (!loading.value) { loading.value = true; startedAt.value = Date.now() }
    },
    onFinish: () => {
      const elapsed = Date.now() - startedAt.value
      setTimeout(() => { loading.value = false }, Math.max(0, minVisibleMs - elapsed))
    },
  })
}, 400)
 
const handleInput = (e) => {
  doSearch()
}

 
</script>

<template>
    <Head title="Product" />

    <AuthenticatedLayout>
        <template #header>

            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Manage Products</h2>
                <Link :href="route('products.create')" class="px-3 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">New Product</Link>
            </div>
        </template>

        <div class="py-12">

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div class="flex flex-wrap items-center justify-end pb-6 space-y-4 flex-column sm:flex-row sm:space-y-0">
                    <div class="relative">
                        <div  class="absolute inset-y-0 left-0 flex items-center pointer-events-none rtl:inset-r-0 rtl:right-0 ps-3">
                            <svg class="w-5 h-5 text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                        </div>
                        <!-- Indicator -->
                        <div v-show="loading" class="absolute inset-y-0 flex items-center pointer-events-none right-2">
                            <svg class="w-4 h-4 text-gray-400 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                            </svg>
                        </div>
                        <input type="text" id="table-search" 
                            v-model="search"
                            @input="handleInput"
                            :aria-busy="loading"
                            class="block p-2 pr-8 text-sm text-gray-900 border border-gray-300 rounded-lg ps-10 w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search for items">

                    </div>
                </div>

                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 rtl:text-right">
                            <thead class="text-xs text-gray-700 uppercase border-b bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        No.
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <Sortable label="Product name" name="name" :query="query" />
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3">                                        
                                        <Sortable label="Price" name="price" :query="query"/>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <Sortable label="Weight" name="weight" :query="query" /> 
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in products.data" :key="product.id" class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        {{ products.meta.from + index }}
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ product.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ product.category.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ product.price_formatted }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ product.weight }}
                                    </td>
                          
                                    <td class="px-6 py-4 space-x-2">
                                        <div class="flex space-x-2">
                                            <!-- Show -->
                                            <Link :href="route('products.show', product.id)"
                                            class="items-center hidden px-2 py-1 text-sm font-medium text-white bg-gray-600 rounded-sm sm:inline-flex hover:text-gray-300"
                                            >
                                            Show
                                            </Link>
                                            <Link :href="route('products.show', product.id)"
                                            class="p-2 text-gray-600 rounded-md sm:hidden hover:text-gray-900"
                                            aria-label="Show"
                                            >
                                            <!-- Heroicon eye -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 
                                                    4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            </Link>

                                            <!-- Edit -->
                                            <Link :href="route('products.edit', product.id)"
                                            class="items-center hidden px-2 py-1 text-sm font-medium text-white bg-blue-600 rounded-sm sm:inline-flex hover:bg-blue-800"
                                            >
                                            Edit
                                            </Link>
                                            <Link :href="route('products.edit', product.id)"
                                            class="p-2 text-blue-600 rounded-md sm:hidden hover:text-blue-800"
                                            aria-label="Edit"
                                            >
                                            <!-- Heroicon pencil -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5h2M12 4v16m7-7H5"/>
                                            </svg>
                                            </Link>

                                            <!-- Delete -->
                                            <button
                                            class="items-center hidden px-2 py-1 text-sm font-medium text-white bg-red-600 rounded-sm sm:inline-flex hover:bg-red-800"
                                            >
                                            Delete
                                            </button>
                                            <button
                                            class="p-2 text-red-600 rounded-md sm:hidden hover:text-red-800"
                                            aria-label="Delete"
                                            >
                                            <!-- Heroicon trash -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 
                                                    21H7.862a2 2 0 01-1.995-1.858L5 7m5 
                                                    4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 
                                                    1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
               
                            </tbody>
                        </table>
                        <!-- Nav -->
                        <Pagination :meta="products.meta" :query="query"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
