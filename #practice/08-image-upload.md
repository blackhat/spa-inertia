# อัปโหลดรูปสินค้า (Laravel 12 + Inertia Vue 3) — ฉบับใช้งานจริง

เอกสารนี้สรุปขั้นตอนครบวงจรตั้งแต่ Migration → Model → Form Request → Controller → Vue (Create.vue)  
---

## 1) Migration: เพิ่มคอลัมน์ `image`  

```bash
php artisan make:migration add_image_to_products_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // เก็บ path ไฟล์ใน disk "public" เช่น images/2025/09/uuid.webp
            $table->string('image')->nullable()->comment('Product image path (storage disk: public)');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
```

รันไมเกรต:
```bash
php artisan migrate
```

สร้าง symlink ให้ `Storage::url()` ใช้งานได้:
```bash
php artisan storage:link
```
ทิปเล็ก ๆ

อย่าลืมรัน php artisan storage:link (ครั้งเดียวพอ) → Laravel จะสร้าง symlink จาก public/storage → storage/app/public




> หมายเหตุ: ในโน้ตเดิมมีบรรทัด `$table->dropColumn('image');` แยกเดี่ยว ๆ — ให้ย้ายไปไว้ใน `down()` ของ migration แทน

---

## 2) Model: `app/Models/Product.php`

- เพิ่ม `image` ใน `$fillable`
- แนะนำเพิ่ม accessor `image_url` เพื่อเรียกใช้รูปได้สะดวกจากหน้า Vue

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'price',
        'weight',
        'category_id',
        'description',
        'image', // ✅ สำคัญ
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    // ...relations
}
```
เวลาเปิดรูปใน browser ต้องเข้าผ่าน url แบบ:
http://localhost:8000/storage/images/2025/09/be692654-e867-4ac8-bbff-7d69f80190bf.png

```vue
<img v-if="product.image_url" 
     :src="product.image_url" 
     alt="Product image" 
     class="object-cover w-24 h-24 border rounded-md" />
```
---

## 3) Form Request: `StoreProductRequest`

กติกาไฟล์ถูกต้องแล้ว (หน่วย `max` = KB → 5MB)

```php
public function rules(): array
{
    return [
        // ...กติกาฟิลด์อื่น ๆ
        'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    ];
}
```

---

## 4) Controller: `ProductController@store`

- ใช้โฟลเดอร์แบบปี/เดือน: `images/Y/m`
- ตั้งชื่อไฟล์เป็น `uuid` + นามสกุลเดิม
- เก็บลง disk `public` เพื่อให้เรียกผ่าน `Storage::url()` ได้

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $payload = $request->validated();

        if ($request->hasFile('image')) {
            $folder   = 'images/' . date('Y/m');
            $ext      = $request->file('image')->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . strtolower($ext);

            // เก็บลง disk public => storage/app/public/...
            // url เรียกใช้ได้ผ่าน Storage::url($path)
            $path = $request->file('image')->storeAs($folder, $fileName, 'public');

            $payload['image'] = $path;
        }

        $request->user()->products()->create($payload);

        return to_route('products.index')->with('success', 'Product created.');
    }
}
```

---

## 5) Frontend (Vue 3 + Inertia): `resources/js/Pages/Product/Create.vue`

จุดสำคัญ:
- `useForm` ให้ `image` เป็น `null` (ไม่ใช่สตริงว่าง)
- เวลา `post` ต้องส่ง `{ forceFormData: true }` เพื่อให้ Inertia แปลงเป็น `FormData` (จำเป็นสำหรับไฟล์)
- ปรับ label จาก “Description” → “Image”
- เพิ่ม `accept` เพื่อคุมชนิดไฟล์ และโชว์ error ของ `form.errors.image`

```vue
<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'

const form = useForm({
  // ฟิลด์อื่น ๆ ...
  'image': null,
})

const handleFileChange = (event) => {
    const image = event.target.files[0];
    if (!image) {
        return
    }
    form.image = image
}

const store = () => {
    form.post(route('products.store'), {
        presserveScroll: true,
        forceFormData: true,
        onSuccess: () => form.reset()
    })
}
</script>

<template>
  <Head title="Create Product" />

  <form @submit.prevent="submit" class="grid grid-cols-6 gap-6">
    <!-- ...อินพุตฟิลด์อื่น ๆ -->

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
    </div>

    <div class="col-span-6">
      <button
        type="submit"
        class="inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-md"
        :disabled="form.processing"
      >
        Save
      </button>
    </div>
  </form>
</template>
```

> ในโน้ตเดิม `label` ยังเขียนว่า “Description” — แก้เป็น “Image” แล้ว  
> ในโน้ตเดิมกำหนด `image: ''` → ควรเป็น `null` และชนิด `File | null` จะเหมาะกับ TS

---

## 6) (แนะนำ) แสดงรูปในตาราง/หน้าแสดงผล

เมื่อมี `image_url` ใน Model แล้ว สามารถใช้ใน Vue ได้ตรง ๆ:

```vue
<img v-if="product.image_url" :src="product.image_url" alt="" class="object-cover w-12 h-12 rounded-md" />
```

---

## 7) เช็กลิสต์สั้น ๆ

- [x] `php artisan migrate`
- [x] `php artisan storage:link`
- [x] เพิ่ม `image` ใน `$fillable`
- [x] สร้าง `image_url` accessor (สะดวกเรียกใช้ใน Vue)
- [x] Form Request รองรับ `image` (5MB, เฉพาะ jpg/jpeg/png/webp)
- [x] Controller เก็บไฟล์ลง `public` disk ด้วย `storeAs()`
- [x] หน้า Create ใช้ `forceFormData: true` ตอน `post`

ถ้าต้องการต่อยอด (อัปเดตรูป/ลบไฟล์เก่า, สร้าง thumbnail ฯลฯ) บอกมาได้ เดี๋ยวจัดให้เป็นเซ็ตต่อเนื่องครับ


Progress
```vue
<input type="file" id="image" @change="handleFileChange" />

    <!-- <progress v-if="form.progress" :value="form.progress.percentage" max="100"
        class="w-full bg-gray-200 rounded-full h-2.5"
    >
        {{ form.progress.percentage }}
    </progress> -->

<div v-if="form.progress" class="w-full bg-gray-200 rounded-full h-2.5">
  <div class="bg-blue-600 h-2.5 rounded-full"
       :style="{ width: form.progress ? form.progress.percentage + '%' : '0%' }">
  </div>
</div>
```

Index.vue
processing
```vue
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
```

image preview
ProductForm.vue
```vue
import { ref } from 'vue'
const imagePreview = ref('')
const handleFileChange = (event) => {
    const image = event.target.files[0]
    if (!image) {
        return
    }
    props.form.image = image
    imagePreview.value = URL.createObjectURL(image)
}

<img class="w-32" :src="imagePreview" v-if="imagePreview"/>

```
Index.vue
```vue
<img class="w-32" :src="product.image_url" v-if="product.image_url"/>
```

“เก็บงานให้เนียน” ต่อ มี 5 quick-wins นี้—โค้ดสั้น ๆ พร้อมใช้เลย

1) ทำ thumbnail ให้ขนาดเท่ากัน
```php
  <img
    :src="product.image_url || '/images/placeholder.png'"
    alt=""
    class="object-cover w-12 h-12 rounded ring-1 ring-gray-200"
  />

```

2) ลบไฟล์เก่าเมื่ออัปเดตรูป (กันไฟล์ค้าง)
```php
// ProductController@update
if ($request->hasFile('image')) {
    $old = $product->image;

    $folder = 'images/' . date('Y/m');
    $ext = $request->file('image')->getClientOriginalExtension();
    $file = Str::uuid().'.'.strtolower($ext);
    $path = $request->file('image')->storeAs($folder, $file, 'public');

    $product->update([...$request->validated(), 'image' => $path]);

    if ($old && Storage::disk('public')->exists($old)) {
        Storage::disk('public')->delete($old);
    }
} else {
    $product->update($request->validated());
}

```
3) ลบไฟล์เมื่อลบสินค้า
```php
// ProductController@destroy
if ($product->image && Storage::disk('public')->exists($product->image)) {
    Storage::disk('public')->delete($product->image);
}
$product->delete();

return back()->with('success', 'Deleted.');

```
5) ตัวอย่าง modal preview (กดที่รูปเพื่อขยาย)
```vue
<script setup>
import { ref } from 'vue'
const preview = ref<string|null>(null)
</script>

<img :src="product.image_url" class="object-cover w-12 h-12 rounded cursor-zoom-in"
     @click="preview = product.image_url" />

<div v-if="preview" class="fixed inset-0 grid bg-black/60 place-items-center" @click="preview=null">
  <img :src="preview" class="max-h-[80vh] max-w-[90vw] rounded shadow-xl" />
</div>

```