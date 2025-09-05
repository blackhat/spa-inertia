<script setup>
import { ref } from 'vue'

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
})

const imagePreview = ref('')

const handleFileChange = (event) => {
    const image = event.target.files[0]
    if (!image) {
        return
    }
    props.form.image = image
    imagePreview.value = URL.createObjectURL(image)
}

const emit = defineEmits(['submit'])
</script>
<template>
                        <form class="relative bg-white rounded-lg shadow" @submit.prevent="emit('submit')">
                            <div class="p-6 space-y-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-6">
                                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Product name</label>
                                        <input type="text" name="name" v-model="form.name" id="name" 
                                            class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                                            :class="{ 'bg-red-50 border-red-500 text-red-900': form.errors.name, 'bg-gray-50 border-gray-300 text-gray-900': !form.errors.name}"
                                            />
                                        <div v-if="form.errors.name" class="mt-2 text-red-500 font-sm">{{ form.errors.name }}</div>
                                        <!-- <label for="name" class="block mb-2 text-sm font-medium text-red-700">Product name</label>
                                        <input type="text" name="name" v-model="form.name" id="name" class="shadow-sm bg-red-50 border border-red-500 text-red-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                                        <div class="mt-2 text-red-500 font-sm">Product name field is required</div> -->
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 ">Brand</label>
                                        <input type="text" name="brand" v-model="form.brand" id="brand" 
                                            class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                                            :class="{ 'bg-red-50 border-red-500 text-red-900': form.errors.brand, 'bg-gray-50 border-gray-300 text-gray-900': !form.errors.brand}"
                                            />
                                        <div v-if="form.errors.brand" class="mt-2 text-red-500 font-sm">{{ form.errors.brand }}</div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 ">Category</label>
                                        <select name="category_id" v-model="form.category_id" id="category_id" 
                                            class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                                            :class="{ 'bg-red-50 border-red-500 text-red-900': form.errors.category_id, 'bg-gray-50 border-gray-300 text-gray-900': !form.errors.category_id}"
                                            >
                                            <option value="">Select a category</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <div v-if="form.errors.category_id" class="mt-2 text-red-500 font-sm">{{ form.errors.category_id }}</div>

                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 ">Price</label>
                                        <input type="number" name="price" v-model="form.price" id="price" 
                                            class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                                            :class="{ 'bg-red-50 border-red-500 text-red-900': form.errors.price, 'bg-gray-50 border-gray-300 text-gray-900': !form.errors.price}"
                                            />
                                        <div v-if="form.errors.price" class="mt-2 text-red-500 font-sm">{{ form.errors.price }}</div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="weight" class="block mb-2 text-sm font-medium text-gray-900 ">Weight</label>
                                        <input type="number" name="weight" v-model="form.weight" id="weight"  
                                            class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                                            :class="{ 'bg-red-50 border-red-500 text-red-900': form.errors.weight, 'bg-gray-50 border-gray-300 text-gray-900': !form.errors.weight}"
                                            />
                                        <div v-if="form.errors.weight" class="mt-2 text-red-500 font-sm">{{ form.errors.weight }}</div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-6">
                                        <label for="image" class="block mb-2 text-sm font-medium text-gray-900">Image</label>
                                        <input
                                            type="file"
                                            name="image"
                                            id="image"
                                            accept=".jpg,.jpeg,.png,.webp"
                                            @change="handleFileChange"
                                            class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                                            :class="{
                                            'bg-red-50 border-red-500 text-red-900': form.errors.image,
                                            'bg-gray-50 border-gray-300 text-gray-900': !form.errors.image
                                            }"
                                        />

                                        <div v-if="form.errors.image" class="mt-2 text-sm text-red-500">
                                            {{ form.errors.image }}
                                        </div>
                                        <!-- <progress v-if="form.progress" :value="form.progress.percentage" max="100"
                                            class="w-full bg-gray-200 rounded-full h-2.5"
                                        >
                                            {{ form.progress.percentage }}
                                        </progress> -->

                                        <div v-if="props.form.progress" class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                            <div
                                                class="bg-blue-600 h-2.5 rounded-full transition-all"
                                                :style="{ width: (props.form.progress?.percentage ?? 0) + '%' }"
                                            />
                                        </div>
  
                                    </div>
                                    <img class="w-32" :src="imagePreview" v-if="imagePreview"/>
                                    <div class="col-span-6 sm:col-span-6">
                                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 ">Description</label>
                                        <textarea name="description" v-model="form.description" id="description" 
                                            class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                                            :class="{ 'bg-red-50 border-red-500 text-red-900': form.errors.description, 'bg-gray-50 border-gray-300 text-gray-900': !form.errors.description}"
                                            >
                                        </textarea>
                                        <div v-if="form.errors.description" class="mt-2 text-red-500 font-sm">{{ form.errors.description }}</div>
                                    </div>       
                                     <div class="col-span-6 space-x-2 sm:col-span-6">
                                        <slot />
                                    </div>
                                </div>
                            </div>
                        </form>
</template>