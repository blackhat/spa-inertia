<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm   } from '@inertiajs/vue3';
import ProductForm from './ProductForm.vue';

defineProps({
    categories: {
        type: Array,
        required: true
    }
})

const form = useForm({
    'name': '',
    'brand': '',
    'category_id': '',
    'price': null,
    'weight': null,
    'image': null, 
    'description': ''
})
 


const store = () => {
    form.post(route('products.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => form.reset()
    })
}
 
</script>

<template>
    <Head title="New Product" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">New Product</h2>
                <Link :href="route('products.index')"  class="px-3 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">Manage products</Link>
            </div>
        </template>

        <div class="py-12">
            
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-center">
                    <div class="relative w-full max-w-2xl max-h-full">
                        <ProductForm :form="form" :categories="categories" @submit="store">
                            
                    <button
                    type="submit"
                    class="inline-flex items-center justify-center text-white bg-blue-700 
                            hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 
                            font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    :disabled="form.processing"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    >
                    <!-- Spinner -->
                    <svg
                        v-if="form.processing"
                        class="w-4 h-4 mr-2 text-white animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>

                    <span>{{ form.processing ? 'Saving...' : 'Save' }}</span>
                    </button>

                            <Link :href="route('products.index')"  class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</Link>
                         </ProductForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>