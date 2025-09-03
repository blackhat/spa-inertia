# ERP2AI Handbook — Accessors & Mutators (Laravel 12)

> ทำให้ Model เป็น “ชั้นกลางอัจฉริยะ” จัดการ data format ตอนเข้า-ออก DB โดยไม่กระทบ logic ที่อื่น

---

## 1) หลักการ
- **Mutator (setXxxAttribute)** ? จัดการค่าก่อน **บันทึกลง DB**  
- **Accessor (getXxxAttribute)** ? จัดการค่าก่อน **ส่งออกจาก Model**

**ข้อดี**
- เขียน logic “ครั้งเดียว” ? ใช้ได้ทุกที่ (Factory, Controller, API, Vue ผ่าน Inertia)
- DB เก็บข้อมูลดิบแบบเหมาะสม แต่ฝั่ง app เรียกใช้สะดวก

---

## 2) ตัวอย่าง: ราคา (เก็บเป็นสตางค์ แต่เรียกเป็นบาท)
```php
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','brand','weight','description','category_id','user_id'];

    // Mutator ? save เป็นสตางค์
    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = (int) round($value * 100);
    }

    // Accessor ? read เป็นบาท
    public function getPriceAttribute($value): float
    {
        return $value / 100;
    }

    // Accessor ? ราคา format string
    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price, 2);
    }
}
```

**ผลลัพธ์**
```php
Product::create(['name'=>'Ring','price'=>199.99]);
// DB เก็บ: 19999
$product->price;            // 199.99
$product->price_formatted;  // "199.99"
```

---

## 3) ตัวอย่าง: รูปภาพ (เก็บไฟล์เนม แต่เรียก URL)
```php
class Product extends Model
{
    protected $fillable = ['name','image'];

    // Mutator ? เก็บเฉพาะชื่อไฟล์
    public function setImageAttribute($value): void
    {
        // ถ้าเป็นไฟล์อัปโหลด
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $path = $value->store('products','public');
            $this->attributes['image'] = $path;
        } else {
            $this->attributes['image'] = $value;
        }
    }

    // Accessor ? คืนค่า URL พร้อมใช้งาน
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : asset('images/no-image.png');
    }
}
```

**ผลลัพธ์**
```php
$product = Product::find(1);
$product->image;     // "products/abc123.jpg"
$product->image_url; // "https://yourapp.test/storage/products/abc123.jpg"
```

---

## 4) ตัวอย่าง: สถานะ (เก็บเป็นตัวเลข แต่เรียกเป็นข้อความ)
```php
class Order extends Model
{
    protected $fillable = ['status'];

    const STATUS_PENDING   = 0;
    const STATUS_APPROVED  = 1;
    const STATUS_REJECTED  = 2;

    // Accessor ? แปลงเป็น label
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }
}
```

**ผลลัพธ์**
```php
$order->status;       // 1
$order->status_label; // "Approved"
```

---

## 5) Checklist การใช้ Accessor/Mutator
- [ ] ใช้เมื่อ “DB เก็บแบบหนึ่ง แต่การใช้งานต้องการอีกแบบ”
- [ ] ตั้งชื่อ attribute ให้ **สื่อความหมาย** (`price_formatted`, `image_url`)
- [ ] ไม่ยัด logic หนัก ๆ ใน accessor (ควรเป็น format เบา ๆ เท่านั้น)
- [ ] คุม type ของค่าที่ return (`float`, `string`) เพื่อ consistency
- [ ] ทดสอบด้วย seeder/factory ให้มั่นใจว่า logic ไม่พัง

---

## 6) Common Mistakes
- ? เขียน logic format ใน Controller ? ซ้ำซ้อน
- ? ให้ Vue/Frontend ไปจัดการเอง ? ไม่สอดคล้องทั้งระบบ
- ? ลืม type cast ? ได้ string/float สลับกัน
- ? เอา logic business (เช่น คำนวณภาษี) ไปใส่ accessor ? ทำให้ query หนัก

---

## 7) Best Practice (ERP Context)
- **เงิน** ? DB เก็บ “สตางค์” (int), accessor คืน “บาท” (float/string)
- **รูปภาพ/ไฟล์** ? DB เก็บ path, accessor คืน URL
- **สถานะ** ? DB เก็บ code/enum, accessor คืน label (ภาษา/คำอ่าน)
- **วันที่** ? DB เก็บ timestamp, accessor คืน format สวยงาม (เช่น `d/m/Y`)

---
