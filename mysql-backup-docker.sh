# Backup
docker exec CONTAINER /usr/bin/mysqldump -u root -pPASSWORD DATABASE > backup.sql
docker exec CONTAINER /usr/bin/mysqldump -u root -pPASSWORD DATABASE | gzip > backup.sql.gz
docker exec CONTAINER /usr/bin/mysqldump -u root -pPASSWORD DATABASE | bzip2 > backup.sql.bz2

# Restore
cat backup.sql | docker exec -i CONTAINER /usr/bin/mysql -u root -pPASSWORD DATABASE
gunzip < backup.sql.gz | docker exec -i CONTAINER /usr/bin/mysql -u root -pPASSWORD DATABASE
bunzip2 < backup.sql.bz2 | docker exec -i CONTAINER /usr/bin/mysql -u root -pPASSWORD DATABASE
