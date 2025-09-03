# 1) �����������ਡ��
cd /path/to/your/project

# 2) ����� git + ignore �ͧ Laravel (�Ӥѭ�ҡ)
git init

# (����ѧ����� .gitignore ������ҧ���� � Ẻ���)
printf "/vendor/\n/node_modules/\n.env\n.env.*\n/public/storage\n/storage/*.key\n/storage/app/*\n!/storage/app/public/\n/storage/framework/*\n/storage/logs/*\n.idea/\n.DS_Store\n" > .gitignore

# 3) ��駪��ͼ����/����� (�������Ƿ������ͧ���)
git config user.name  "blackhat"
git config user.email "you@example.com"

# 4) commit �á
git add .
git commit -m "chore: initial commit (Laravel + pagination component)"

# 5) ���ҧ repo ���Һ� GitHub ������� URL ����� (���͡ HTTPS ���� SSH ���ҧ����ҧ˹��)
git remote add origin https://github.com/blackhat/erp2ai.git
# ���Ͷ�ҵ�� SSH �������:
# git remote add origin git@github.com:blackhat/erp2ai.git

# 6) push ��� main
git branch -M main
git push -u origin main
