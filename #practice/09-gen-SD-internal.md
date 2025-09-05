```md
# Laravel × Stable Diffusion API (7860 FastAPI) — Seed รูปสินค้าอัตโนมัติ

## 0) Prerequisites
- Laravel 10/11/12 + PHP ≥ 8.1  
- เปิด SD WebUI (Automatic1111) พร้อม API (`/docs` ต้องเข้าได้)  
- โครงการ Laravel ทำ `php artisan storage:link` แล้ว  
- ใช้ **PostgreSQL** หรือ DB อะไรก็ได้ (ไม่เกี่ยวกับขั้นตอนนี้)

---

## 1) ทดสอบ API จากหน้า `/docs` ก่อน
เปิดเบราว์เซอร์ไปที่ `http://192.168.0.99:7860/docs`

1) เลื่อนหา `POST /sdapi/v1/txt2img` → กด **Try it out**  
2) วาง JSON นี้ลงใน **Request body** แล้วกด **Execute** (ปรับ prompt ได้ตามใจ):

~~~json
{
  "prompt": "portrait photo of a young woman, clean background, product demo style, soft light, highly detailed, 35mm photo",
  "negative_prompt": "low quality, deformed, extra limbs, text, watermark, logo",
  "width": 640,
  "height": 800,
  "steps": 28,
  "cfg_scale": 7,
  "sampler_name": "DPM++ 2M Karras",
  "seed": -1,
  "batch_size": 1
}
~~~

3) จะได้ **response** ที่มี `images: [ "<base64>" ]` → แปลว่าพร้อมใช้งานจาก Laravel แล้ว

ทดสอบด้วย cURL ก็ได้:
~~~bash
curl -X POST "http://192.168.0.99:7860/sdapi/v1/txt2img" \
  -H "Content-Type: application/json" \
  -d '{"prompt":"portrait photo, soft light","width":640,"height":800,"steps":25,"cfg_scale":7,"seed":-1}'
~~~

---

## 2) ตั้งค่า ENV ให้ Laravel
เพิ่มใน `.env`:
~~~env
SD_BASE_URL=http://192.168.0.99:7860
~~~

เพิ่มใน `config/services.php`:
~~~php
'stable_diffusion' => [
    'base_url' => env('SD_BASE_URL', 'http://127.0.0.1:7860'),
],
~~~

---

## 3) สร้าง Service เรียก SD API
`app/Services/StableDiffusion.php`
~~~php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StableDiffusion
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.stable_diffusion.base_url', 'http://127.0.0.1:7860');
    }

    public function txt2imgToStorage(string $prompt, string $negativePrompt = ''): string
    {
        $payload = [
            'prompt'          => $prompt,
            'negative_prompt' => $negativePrompt,
            'width'           => 640,
            'height'          => 800,
            'steps'           => 28,
            'cfg_scale'       => 7,
            'sampler_name'    => 'DPM++ 2M Karras',
            'seed'            => -1,
            'batch_size'      => 1,
        ];

        $resp = Http::timeout(180)
            ->post("{$this->baseUrl}/sdapi/v1/txt2img", $payload)
            ->throw()
            ->json();

        $b64 = $resp['images'][0] ?? null;
        if (!$b64) {
            throw new \RuntimeException('SD API returned no image.');
        }

        $b64 = preg_replace('/^data:image\/\w+;base64,/', '', $b64);

        $folder = 'images/' . date('Y/m');
        $file   = Str::uuid() . '.png';
        $path   = "{$folder}/{$file}";

        Storage::disk('public')->put($path, base64_decode($b64));

        return $path;
    }
}
~~~

---

## 4) Seeder สำหรับสร้างสินค้า + รูปจาก SD
~~~bash
php artisan make:seeder ProductImageSeeder
php artisan make:factory ProductFactory --model=Product
~~~

`database/seeders/ProductImageSeeder.php`
~~~php
<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\StableDiffusion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $sd = app(StableDiffusion::class);

        for ($i = 1; $i <= 8; $i++) {
            $name = fake()->sentence(3);
            $prompt = "portrait of a modern asian model, product demo style, soft studio light, highly detailed, natural skin, looking at camera";
            $neg    = "low quality, text, watermark, logo, deformed, extra fingers";

            $imagePath = $sd->txt2imgToStorage($prompt, $neg);

            Product::create([
                'name'        => $name,
                'brand'       => fake()->company(),
                'category_id' => 1,
                'price'       => fake()->numberBetween(1, 9),
                'weight'      => fake()->randomFloat(2, 0.5, 3.0),
                'description' => fake()->paragraph(),
                'image'       => $imagePath,
            ]);
        }
    }
}
~~~

เพิ่มใน `DatabaseSeeder.php`:
~~~php
public function run(): void
{
    $this->call(ProductImageSeeder::class);
}
~~~

รัน:
~~~bash
php artisan db:seed --class=ProductImageSeeder
# หรือ
php artisan migrate:fresh --seed
~~~

---

## 5) Product Model เพิ่ม accessor
~~~php
use Illuminate\Support\Facades\Storage;

protected $appends = ['image_url'];

public function getImageUrlAttribute(): ?string
{
    return $this->image ? Storage::url($this->image) : null;
}
~~~

---

## 6) Vue Component แสดงรูป
~~~vue
<img :src="product.image_url"
     alt=""
     class="object-cover w-12 h-12 rounded ring-1 ring-gray-200"
     loading="lazy" />
~~~

---

## 7) Troubleshooting
- Timeout → ลด steps / เพิ่ม timeout  
- Image ไม่ขึ้น → ลืม `php artisan storage:link`  
- อยากหลายภาพ → ปรับ `batch_size` แล้ววน `resp['images']`

---

## 8) Bonus: ใช้ไฟล์ local
ใส่ไฟล์ไว้ `storage/app/public/seed-images/` แล้วใน Seeder:
~~~php
$images = Storage::disk('public')->files('seed-images');
$imagePath = $images[array_rand($images)];
~~~

---

## Prompt Example
- สวยใส:  


คุณอยากให้ผมลองรวบรวม ชื่อ checkpoint / LoRA ที่นิยมใช้กับ jewelry จาก HuggingFace / CivitAI มั้ยครับ?