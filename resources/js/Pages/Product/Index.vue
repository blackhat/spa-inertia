<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'

const props =  defineProps({
    products: {
        Type: Object,
        required: true
    },
})

 
</script>

<template>
    <Head title="Product" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Product List
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                                        Product name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Weight
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in products.data" :key="products.id" class="bg-white border-b hover:bg-gray-50">
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
                                            <button
                                            class="items-center hidden px-2 py-1 text-sm font-medium text-white bg-gray-600 rounded-sm sm:inline-flex hover:text-gray-300"
                                            >
                                            Show
                                            </button>
                                            <button
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
                                            </button>

                                            <!-- Edit -->
                                            <button
                                            class="items-center hidden px-2 py-1 text-sm font-medium text-white bg-blue-600 rounded-sm sm:inline-flex hover:bg-blue-800"
                                            >
                                            Edit
                                            </button>
                                            <button
                                            class="p-2 text-blue-600 rounded-md sm:hidden hover:text-blue-800"
                                            aria-label="Edit"
                                            >
                                            <!-- Heroicon pencil -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5h2M12 4v16m7-7H5"/>
                                            </svg>
                                            </button>

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
                        <Pagination :meta="products.meta" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
