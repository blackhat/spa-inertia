## Create Product

ProductController.php
```php
    public function create() {
        $categories = Category::orderBy('name')->get();
        return inertia('Product/Create', [
            'categories' => $categories->toResourceCollection()
        ]);
    }

    public function store(Request  $request) {
        $request->user()->products()->create($request->all());
        return redirect()->route('products.index');
    }

```
Product/Index.vue
```vue
        <template #header>

            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Manage Products</h2>
                <Link :href="route('products.create')" class="px-3 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">New Product</Link>
            </div>
        </template>
```
Product/Create.vue

```vue
<script setup>
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
    'price': '',
    'weight': '',
    'description': '',
})

const store = () => {
    form.post(route('products.store'), {
        onSuccess: () => form.reset()
    })
}



</script>

<template>
<!-- ใส่ Link -->
<!-- ใส่ @submit -->
<form @submit.prevent="store">
<!-- v-model all input -->
<input v-model="name">
<!-- เรียกรายการ categories -->    
<select v-model="category_id">
    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
</form>

</template>

```

## Next we change to validation using StoreProductRequest
```bash
php artisan make:request StoreProductRequest
```

```php
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:5'],
            'brand' => ['required', 'min:5'],
            'category_id' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'weight' => ['required', 'numeric'],
            'description' => ['max:50'],
        ];
    }

    public function attributes()
    {
        return [
            'category_id' => 'Category'
        ];
    }
```

ProductController@store
```php
    public function store(StoreProductRequest  $request)
    {
        $request->user()->products()->create($request->validated());
        return redirect()->route('products.index');
    }
```
add 'model-adjustment.md'
```php
use Illuminate\Database\Eloquent\Casts\Attribute;

    protected function price(): Attribute
    {
        return Attribute::make(
            set: fn (int $value) => $value * 100,
            get: fn (int $value) => $value / 100
        );
    }
```
Index.vue
```vue
<!-- form.errors-->
        <div class="col-span-6 sm:col-span-3">
            <label for="price" class="block mb-2 text-sm font-medium text-gray-900 ">Price</label>
            <input type="number" name="price" v-model="form.price" id="price" 
                class="shadow-sm border text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5"
                :class="{ 'bg-red-50 border-red-500 text-red-900': form.errors.price, 'bg-gray-50 border-gray-300 text-gray-900': !form.errors.price}"
                />
            <div v-if="form.errors.price" class="mt-2 text-red-500 font-sm">{{ form.errors.price }}</div>
        </div>

```

refactor -> ProductForm.vue

```vue
<script setup>
defineProps({
    form: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['submit']) 
</setup>

<template>
<form @submit.prevent="emit('submit')">

    <div>
    <!-- slot for button -->
        <slot />
    </div>
</template>
```
Index.vue

```vue
<script setup>
    import ProductForm from './ProductForm.vue';
</script>
<template>
    <ProductForm :form="form" :categories="categories" @submit="store">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save</button>
                    <Link :href="route('products.index')"  class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</Link>
        </ProductForm>
</template>
```