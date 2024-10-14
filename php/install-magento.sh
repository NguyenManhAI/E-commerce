MYSQL_USER=$1
MYSQL_PASSWORD=$2
MYSQL_DATABASE=$3
USERNAME_MAGENTO_KEY=$4
PASSWORD_MAGENTO_KEY=$5

# tạo thư mục magento trong composer:/app
mkdir -p ./magento && chmod -R u+w ./magento && rm -rf ./magento/* && rm -rf ./magento/.[!.]*
# chmod -R u+w ./magento && rm -rf ./magento/* && rm -rf ./magento && mkdir -p ./magento

composer config --no-interaction allow-plugins.php-http/discovery true

# thiết lập username và password (authentication keys)
composer config --global http-basic.repo.magento.com $USERNAME_MAGENTO_KEY $PASSWORD_MAGENTO_KEY

# tạo project magento
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=2.4.7-p3 magento

cd magento
find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} +
find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} +
chown -R :www-data . # Ubuntu
chmod u+x bin/magento

bin/magento setup:install \
--base-url=http://localhost:8080/ \
--db-host=mysql \
--db-name=$MYSQL_DATABASE \
--db-user=$MYSQL_USER \
--db-password=$MYSQL_PASSWORD \
--admin-firstname=admin \
--admin-lastname=admin \
--admin-email=admin@admin.com \
--admin-user=admin \
--admin-password=admin123 \
--language=en_US \
--currency=USD \
--timezone=Asia/Ho_Chi_Minh \
--use-rewrites=1 \
--search-engine=elasticsearch8 \
--elasticsearch-host=elasticsearch \
--elasticsearch-port=9200 \
--elasticsearch-index-prefix=magento2 \
--elasticsearch-timeout=15
