%w(
  php
  php-mbstring
  php-mcrypt
  php-xml
  php-pdo
  php-pgsql
  php-pecl-memcache
  php-pecl-redis
  php-gd
).each do |pkg|
  package pkg do
    action :install
    options '--enablerepo=remi,remi-php56'
  end
end

execute 'install composer' do
  command 'curl -sS https://getcomposer.org/installer | php'
  not_if 'which composer'
end

execute 'enable composer global' do
  command 'mv composer.phar /usr/local/bin/composer'
  only_if 'which composer && test -f composer.phar'
end


# php-fpm
#
package 'php-fpm' do
  action :install
  options '--enablerepo=remi,remi-php56'
end

%w(
  /var/run/php-fpm
  /var/log/php-fpm
).each do |dir|
  directory dir do
    action :create
    mode '0755'
    owner 'root'
    group 'root'

    not_if "test -e #{dir}"
  end
end

template '/etc/php-fpm.d/www.conf' do
  notifies :restart, 'service[php-fpm]'
end

service 'php-fpm' do
  action [:start, :enable]
end

#template '/etc/php.ini' do
#  notifies :restart, 'service[php-fpm]'
#end
