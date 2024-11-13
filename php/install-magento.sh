
# tạo thư mục magento trong composer:/app
mkdir -p ./magento && chmod -R u+w ./magento && rm -rf ./magento/* && rm -rf ./magento/.[!.]*
# chmod -R u+w ./magento && rm -rf ./magento/* && rm -rf ./magento && mkdir -p ./magento

composer config --no-interaction allow-plugins.php-http/discovery true

# Set up username and password (authentication keys)
composer config --global http-basic.repo.magento.com $USERNAME_MAGENTO_KEY $PASSWORD_MAGENTO_KEY

# creat project magento
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=2.4.7-p3 magento

cd magento
find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} +
find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} +
chown -R :www-data . # Ubuntu
chmod u+x bin/magento

bin/magento setup:install \
--base-url=http://localhost:8000/ \
--db-host=mysql \
--db-name=$MYSQL_DATABASE \
--db-user=$MYSQL_USER \
--db-password=$MYSQL_PASSWORD \
--admin-firstname=$ADMIN_FIRSTNAME \
--admin-lastname=$ADMIN_LASTNAME \
--admin-email=$ADMIN_EMAIL \
--admin-user=$ADMIN_USER \
--admin-password=$ADMIN_PASSWORD \
--language=vi_VN \
--currency=VND \
--timezone=Asia/Ho_Chi_Minh \
--use-rewrites=1 \
--search-engine=opensearch \
--opensearch-host=opensearch \
--opensearch-port=9200 \
--opensearch-index-prefix=magento2 \
--opensearch-timeout=15

# Turn off 2-step authentication
bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth
bin/magento cache:flush 

# install cron job
bin/magento cron:install --force

# execute reindex required
bin/magento cron:run
# bin/magento indexer:reindex

