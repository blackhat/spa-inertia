# Database Schema Design Checklist (Laravel 12 + ERP Context)

## 1. พื้นฐานที่ต้องเข้าใจ
- ? Relational Database Concept: ตาราง, แถว, คอลัมน์, Primary Key (PK), Foreign Key (FK)
- ? Data Types: `string`, `text`, `integer`, `decimal`, `boolean`, `date`, `timestamp`
- ? Naming Convention:
  - ตารางใช้ **พหูพจน์** (products, categories)
  - PK มาตรฐานเป็น `id`
  - FK เป็น `xxx_id` (user_id, category_id)

---

## 2. ความสัมพันธ์ (Relationships)
- **One-to-Many**:  
  - User ? Products  
  - Category ? Products
- **Many-to-Many**:  
  - Product ? Tag (pivot table: product_tag)
- **One-to-One**:  
  - User ? Profile
- **Self-Referencing**:  
  - Category ? Subcategory (parent_id)

---

## 3. ลำดับการสร้าง Migration
- Parent ก่อน Child  
  - ? categories  
  - ? users  
  - ? products (ต้องมาทีหลัง)
- ถ้ามี pivot ? มาทีหลังสุด

---

## 4. Data Type & Precision
- **เงิน**: `unsignedBigInteger` (เก็บเป็นสตางค์) หรือ `decimal(12,2)`  
- **น้ำหนักโลหะ**: `decimal(8,3)` (รองรับทศนิยมละเอียด)  
- **ข้อความยาว**: ใช้ `text` ไม่ใช้ `string`  
- **Boolean**: ใช้ `boolean` หรือ `tinyInteger(1)`

---

## 5. Constraints & Index
- **Primary Key (PK)** ? `id()`
- **Foreign Key (FK)** ? `foreignId('category_id')->constrained()`
- **Unique** ? `unique()` เช่น email
- **Index** ? `index()` สำหรับ column ที่ query บ่อย เช่น code, name

---

## 6. Best Practices
- ใส่ `->comment('...')` ทุกคอลัมน์ (อ่านง่าย, ช่วยเวลา audit)
- แยก `created_at`, `updated_at` ? ใช้ `$table->timestamps()`
- ใช้ soft delete ถ้าต้องการกู้คืน ? `$table->softDeletes()`
- คิดเรื่อง scale:
  - product code ? unique
  - category tree ? parent_id + nested set / recursive query
  - search ? อาจต้องเพิ่ม fulltext index หรือ vector search

---

## 7. Seeder & Factory
- Parent ? Child เช่น สร้าง Categories ก่อน Products
- Factory ใช้ `state()` สำหรับผูก FK แบบ dynamic
- ใช้ `DatabaseSeeder` รวมลำดับการ seed

---

## 8. Workflow ที่แนะนำ (สำหรับ ERP2AI/ระบบจริง)
1. **ออกแบบ schema บนกระดาษ/diagrams** (ERD)
2. **สร้าง migration ตามลำดับ** (parent ? child)
3. **ใส่ relation ใน model**
4. **ทำ factory/seeder**
5. **ทดสอบ migrate:fresh --seed**
6. **ปรับแก้ schema ให้ครบก่อนลงข้อมูลจริง**

---

## 9. เครื่องมือเสริม
- ERD Tools: dbdiagram.io, draw.io, MySQL Workbench
- Laravel Package: `doctrine/dbal` (สำหรับแก้ไข column)
- Migration squashing (ลดไฟล์เก่า): `php artisan schema:dump`

---

## 10. ข้อผิดพลาดที่เจอบ่อย
- ? ลำดับ migration สลับ (child ก่อน parent)
- ? ชนิดข้อมูล FK ไม่ตรง (bigint vs int)
- ? ลืมใส่ index ? query ช้า
- ? เก็บเงินเป็น float ? ค่าคลาดเคลื่อน
- ? ออกแบบ schema ตามโค้ด ? ไม่ได้เผื่อ scale ธุรกิจ

---
