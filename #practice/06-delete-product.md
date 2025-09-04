## Delete

ProductController@destroy
```php
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }
```


 Edit.vue
 ```vue
 <script setup>

import { router } from '@inertiajs/vue3'
 
const deleteRow = (id) => {
    if (window.confirm("Are you sure to delete?")) {
        router.delete(route('products.destroy', id), {
            preserveScroll: true
        })
    }
}
 </script>

 <template>
    <div class="flex items-center justify-between">
        <div class="space-x-2">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update</button>
            <Link :href="route('products.index')" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</Link>
        </div>
        <a href="#" class="font-medium text-red-600 hover:underline" @click.prevent="deleteRow(product.id)">Delete</a>
    </div>
 </template>
```

 Index.vue
 ```vue
 <script setup>

 import { router } from '@inertiajs/vue3'

const deleteRow = (id) => {
    if (window.confirm("Are you sure?")) {
        router.delete(route('products.destroy', id), {
            preserveScroll: true
        })
    }
}
 
 </script>

 <template>
    <button
    class="items-center hidden px-2 py-1 text-sm font-medium text-white bg-red-600 rounded-sm sm:inline-flex hover:bg-red-800"
    @click.prevent="deleteRow(product.id)"
    >
    Delete
    </button>
</template>
 ```