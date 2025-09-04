# 1) เข้าโฟลเดอร์โปรเจกต์
cd /path/to/your/project

# 2) เริ่ม git + ignore ของ Laravel (สำคัญมาก)
git init

# (ถ้ายังไม่มี .gitignore ให้สร้างเร็ว ๆ แบบนี้)
printf "/vendor/\n/node_modules/\n.env\n.env.*\n/public/storage\n/storage/*.key\n/storage/app/*\n!/storage/app/public/\n/storage/framework/*\n/storage/logs/*\n.idea/\n.DS_Store\n" > .gitignore

# 3) ตั้งชื่อผู้ใช้/อีเมล (ครั้งเดียวที่เครื่องนั้น)
git config user.name  "blackhat"
git config user.email "you@example.com"

# 4) commit แรก
git add .
git commit -m "chore: initial commit (Laravel + pagination component)"

# 5) สร้าง repo เปล่าบน GitHub แล้วเอา URL มาใส่ (เลือก HTTPS หรือ SSH อย่างใดอย่างหนึ่ง)
git remote add origin https://github.com/blackhat/erp2ai.git
# หรือถ้าตั้ง SSH ไว้แล้ว:
# git remote add origin git@github.com:blackhat/erp2ai.git

# 6) push ขึ้น main
git branch -M main
git push -u origin main



check before use git

pwd
git remote -v
git status 

git pull origin main

เกิดเพราะมี “งานค้างในเครื่อง” (ทั้งแก้ไฟล์เดิมและไฟล์ใหม่ที่ยังไม่ถูก track) ซึ่งจะถูกทับโดยของรีโมตเวลา pull
git add -A
git commit -m "wip: Product Create/Show/Edit + ProductForm (pre-pull)"
git pull --rebase

ถ้ามีคอนฟลิกต์:

# แก้ไฟล์ที่ขัดกัน → git add <ไฟล์ที่แก้แล้ว>
git rebase --continue


