<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router   } from '@inertiajs/vue3'
import ProductForm from './ProductForm.vue' 

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
})

const form = useForm({ ...props.product, category_id: props.product.category.id })

 
const update = () => {
    form.put(route('products.update', form.id), {
        onSuccess: () => form.reset()
    })
}

const deleteRow = (id) => {
    if (window.confirm("Are you sure to delete?")) {
        router.delete(route('products.destroy', id), {
            preserveScroll: true
        })
    }
}
 
</script>

<template>
    <Head title="Edit Product" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Product</h2>
                <Link :href="route('products.index')"  class="px-3 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">Manage products</Link>
            </div>
        </template>

        <div class="py-12">
            
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-center">
                    <div class="relative w-full max-w-2xl max-h-full">
                        <ProductForm :form="form" :categories="categories" @submit="update">
                            <div class="flex items-center justify-between">
                                <div class="space-x-2">
                                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update</button>
                                    <Link :href="route('products.index')" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</Link>
                                </div>
      
                                    <button type="button"
                                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                    aria-label="Delete"
                                    @click.prevent="deleteRow(product.id)"
                                    >
                                    delete
                                    </button>
                            </div>
                        </ProductForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>