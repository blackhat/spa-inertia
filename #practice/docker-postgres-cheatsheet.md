# Docker Postgres Lifecycle Cheat Sheet

คู่มือย่อสำหรับการใช้ Postgres ผ่าน Docker (dev environment)

---

## 🚀 Start / Stop / Status

```bash
# สตาร์ท container (background)
docker compose up -d

# ปิด container
docker compose down

# ปิดพร้อมลบ volume (reset data)
docker compose down -v

# ดูสถานะ container
docker compose ps

# ดู log ของ service pg
docker compose logs -f pg
```

---

## 🔄 Reset / Re-init Database

```bash
# ปิด container
docker compose down

# ลบ volume ชื่อ pgdata (ข้อมูลหายหมด)
docker volume rm <project>_pgdata
# หรือถ้าใช้ global name:
docker volume rm pgdata

# สตาร์ทใหม่ → init DB, user, password ตาม docker-compose.yml
docker compose up -d
```

หรือสั้น ๆ:
```bash
docker compose down -v
docker compose up -d
```

---

## 🗄️ เข้าไปใน DB

```bash
# เข้า shell ของ container
docker exec -it pg bash

# เปิด psql ภายใน container
psql -U app -d app
# ออก: \q
```

---

## 💾 Backup & Restore

```bash
# backup (บันทึกไฟล์ SQL ออกมา)
docker exec -t pg pg_dump -U app app > backup.sql

# restore (โหลดไฟล์ SQL เข้า DB)
docker exec -i pg psql -U app -d app < backup.sql
```

---

## 📝 Tips

- `.env` ใน Laravel:
  ```env
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=app
  DB_USERNAME=app
  DB_PASSWORD=secret

  SESSION_DRIVER=file
  CACHE_DRIVER=file
  ```

- ถ้าเปลี่ยน `POSTGRES_USER` / `POSTGRES_PASSWORD` / `POSTGRES_DB` → ต้อง **reset volume** (`down -v`) ก่อนถึงจะมีผล
- ใช้ `docker compose down` เฉย ๆ ข้อมูลยังอยู่ใน volume → เปิดกลับมาข้อมูลไม่หาย
- ใช้ `docker compose down -v` ข้อมูลหายหมด (clean slate)


---
## basic docker

# 🐳 Docker Concept Cheat Sheet

## 1. Image
- เหมือน **แม่พิมพ์ (template)** หรือไฟล์ ISO
- สร้างจาก Dockerfile หรือดึงจาก Docker Hub เช่น `postgres:16`
- Immutable (เปลี่ยนไม่ได้ตรง ๆ)  
- ขนาดใหญ่ โหลดครั้งเดียว เก็บ cache ไว้ในเครื่อง → container หลายตัวใช้ร่วมกันได้

---

## 2. Container
- คือ **instance ที่รันจริง** จาก image
- แต่ละ container = process แยก, filesystem แยก
- ลบ container → ตัวรันหาย แต่ image ยังอยู่
- มี config เฉพาะ เช่น:
  - ports (`5432:5432`)
  - env (`POSTGRES_USER=app`)
  - networks
  - volumes

👉 จะมีหลาย container ใช้ image เดียวกันได้ เช่น
- `erp_pg` → ใช้ postgres:16
- `n8n_pg` → ใช้ postgres:16

---

## 3. Volume
- **พื้นที่เก็บข้อมูลถาวร** (persistent storage)
- Mount จาก host → container
- ถ้าไม่ใช้ volume: data อยู่ใน container → `docker rm` แล้วหายหมด
- ใช้ volume: data แยกอยู่นอก container → container พัง ลบ สร้างใหม่ data ยังอยู่

สองแบบหลัก:
1. **Named volume** (Docker จัดการเอง)  
   ```yaml
   volumes:
     - pgdata:/var/lib/postgresql/data
   volumes:
     pgdata:

---

→ Docker จะเก็บใน /var/lib/docker/volumes/pgdata/_data

# 2.Bind mount (เจาะ path host เอง)

```yaml
volumes:
  - ./docker-data/pg:/var/lib/postgresql/data
```
→ ข้อมูลอยู่ในโปรเจกต์ เห็นไฟล์จริง

4. เปรียบเทียบแบบเข้าใจง่าย
| สิ่งนี้   | เปรียบเหมือน                | ตัวอย่าง             |
| --------- | --------------------------- | -------------------- |
| Image     | ไฟล์ติดตั้ง Windows (ISO)   | `postgres:16`        |
| Container | Windows ที่ติดตั้งแล้ว (VM) | `erp_pg`, `n8n_pg`   |
| Volume    | HDD แยกออกมาเก็บข้อมูล      | `pgdata`, `n8n_data` |

5. คำสั่งที่ใช้บ่อย

```bash
# ดู container ที่รันอยู่
docker ps

# ดู container ทั้งหมด (รวมที่หยุด)
docker ps -a

# ดู image ที่โหลดมาแล้ว
docker images

# ดู volume
docker volume ls

# ลบ container
docker rm -f <id|name>

# ลบ volume
docker volume rm <name>

# ลบ image
docker rmi <image>
```

💡 สรุป:

- Image = template

- Container = instance ที่รัน

- Volume = data จริง


