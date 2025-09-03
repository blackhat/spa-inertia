# Cheat Sheet: Laravel Resource & Relation

## 1) One-to-One / Many-to-One (belongsTo, hasOne)
```php
'category' => CategoryResource::make($this->whenLoaded('category')),
```
- ใช้ `Resource::make(...)`
- ถ้า relation ยังไม่ได้ `with('category')` ? field จะเป็น `null`

---

## 2) One-to-Many / Many-to-Many (hasMany, belongsToMany)
```php
'products' => ProductResource::collection($this->whenLoaded('products')),
```
- ใช้ `Resource::collection(...)`
- คืน array ของ resource

---

## 3) ดึง field เดียวจาก relation
```php
'category_name' => $this->whenLoaded('category', fn () => $this->category->name),
```
- คืนค่า field เดียว เช่น `name`
- ใช้เวลาที่ไม่อยากส่ง resource ซ้อน

---

## 4) ซ่อน relation ถ้าไม่ได้โหลด
```php
'category' => $this->when(
    $this->relationLoaded('category'),
    fn () => new CategoryResource($this->category)
),
```
- ใช้ `relationLoaded()` แทน `whenLoaded()` ถ้าอยากควบคุมชัดเจน

---

## 5) Tips
- ใช้ `with(['category','user'])` ใน Controller เพื่อป้องกัน N+1
- ใช้ `make()` เมื่อเป็น object เดียว
- ใช้ `collection()` เมื่อเป็น array/collection
- ใช้ `whenLoaded()` เมื่อไม่แน่ใจว่า relation ถูกโหลดหรือยัง
- ใช้ `when()` + `relationLoaded()` เมื่ออยากเงื่อนไขละเอียดกว่า

---
# Laravel Resource — กรณีต้องใช้ `collection()` และตาราง Mapping Relation

## 1) กรณีที่ต้องทำ `Resource::collection()`

ใช้เมื่อ relation คืนค่ามาเป็น **หลาย record** และเราต้องการส่งข้อมูลลูกทั้งหมดไปที่ frontend เช่น:

- **Category ? Products (hasMany)**  
  ```php
  'products' => ProductResource::collection($this->whenLoaded('products')),
  ```
  แสดงสินค้าทั้งหมดในหมวดหมู่

- **Order ? Items (hasMany)**  
  ```php
  'items' => OrderItemResource::collection($this->whenLoaded('items')),
  ```
  แสดงสินค้าในใบสั่งซื้อ

- **User ? Roles (belongsToMany)**  
  ```php
  'roles' => RoleResource::collection($this->whenLoaded('roles')),
  ```
  แสดง role ทั้งหมดของ user

- **Post ? Comments (morphMany)**  
  ```php
  'comments' => CommentResource::collection($this->whenLoaded('comments')),
  ```
  แสดงคอมเมนต์ทั้งหมดของโพสต์

---

## 2) ตาราง Mapping Relation ? การใช้ Resource

| ประเภท Relation          | ตัวอย่าง Model ? Relation      | ใช้ Resource แบบไหน |
|---------------------------|---------------------------------|----------------------|
| `belongsTo`               | Product ? Category              | `Resource::make()`   |
| `hasOne`                  | User ? Profile                  | `Resource::make()`   |
| `hasMany`                 | Category ? Products             | `Resource::collection()` |
| `belongsToMany`           | User ? Roles                    | `Resource::collection()` |
| `morphOne`                | Post ? Image                    | `Resource::make()`   |
| `morphMany`               | Post ? Comments                 | `Resource::collection()` |
| `morphTo`                 | Comment ? commentable (Post/User)| `Resource::make()`   |
| `morphToMany`             | Tag ? Posts, Videos             | `Resource::collection()` |

---

## 3) Tips
- ใช้ `make()` ? เมื่อ relation คืน object เดียว (one-to-one, many-to-one)  
- ใช้ `collection()` ? เมื่อ relation คืนหลาย object (one-to-many, many-to-many)  
- ใช้ `whenLoaded('relation')` ? ป้องกัน N+1 และไม่ส่ง relation ที่ไม่ได้ eager load  
- ใช้ `when()` + `relationLoaded()` ? ถ้าอยากควบคุมละเอียดว่า field จะโชว์เมื่อไร  

---
