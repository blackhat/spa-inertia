# Database Schema Design Checklist (Laravel 12 + ERP Context)

## 1. ��鹰ҹ����ͧ����
- ? Relational Database Concept: ���ҧ, ��, �������, Primary Key (PK), Foreign Key (FK)
- ? Data Types: `string`, `text`, `integer`, `decimal`, `boolean`, `date`, `timestamp`
- ? Naming Convention:
  - ���ҧ�� **��پ���** (products, categories)
  - PK �ҵðҹ�� `id`
  - FK �� `xxx_id` (user_id, category_id)

---

## 2. ��������ѹ�� (Relationships)
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

## 3. �ӴѺ������ҧ Migration
- Parent ��͹ Child  
  - ? categories  
  - ? users  
  - ? products (��ͧ�ҷ���ѧ)
- ����� pivot ? �ҷ���ѧ�ش

---

## 4. Data Type & Precision
- **�Թ**: `unsignedBigInteger` (����ʵҧ��) ���� `decimal(12,2)`  
- **���˹ѡ����**: `decimal(8,3)` (�ͧ�Ѻ�ȹ��������´)  
- **��ͤ������**: �� `text` ����� `string`  
- **Boolean**: �� `boolean` ���� `tinyInteger(1)`

---

## 5. Constraints & Index
- **Primary Key (PK)** ? `id()`
- **Foreign Key (FK)** ? `foreignId('category_id')->constrained()`
- **Unique** ? `unique()` �� email
- **Index** ? `index()` ����Ѻ column ��� query ���� �� code, name

---

## 6. Best Practices
- ��� `->comment('...')` �ء������� (��ҹ����, �������� audit)
- �¡ `created_at`, `updated_at` ? �� `$table->timestamps()`
- �� soft delete ��ҵ�ͧ��á��׹ ? `$table->softDeletes()`
- �Դ����ͧ scale:
  - product code ? unique
  - category tree ? parent_id + nested set / recursive query
  - search ? �Ҩ��ͧ���� fulltext index ���� vector search

---

## 7. Seeder & Factory
- Parent ? Child �� ���ҧ Categories ��͹ Products
- Factory �� `state()` ����Ѻ�١ FK Ẻ dynamic
- �� `DatabaseSeeder` ����ӴѺ��� seed

---

## 8. Workflow ����й� (����Ѻ ERP2AI/�к���ԧ)
1. **�͡Ẻ schema ����д��/diagrams** (ERD)
2. **���ҧ migration ����ӴѺ** (parent ? child)
3. **��� relation � model**
4. **�� factory/seeder**
5. **���ͺ migrate:fresh --seed**
6. **��Ѻ�� schema ���ú��͹ŧ�����Ũ�ԧ**

---

## 9. ����ͧ��������
- ERD Tools: dbdiagram.io, draw.io, MySQL Workbench
- Laravel Package: `doctrine/dbal` (����Ѻ��� column)
- Migration squashing (Ŵ������): `php artisan schema:dump`

---

## 10. ��ͼԴ��Ҵ����ͺ���
- ? �ӴѺ migration ��Ѻ (child ��͹ parent)
- ? ��Դ������ FK ���ç (bigint vs int)
- ? ������ index ? query ���
- ? ���Թ�� float ? ��Ҥ�Ҵ����͹
- ? �͡Ẻ schema ����� ? ��������� scale ��áԨ

---
