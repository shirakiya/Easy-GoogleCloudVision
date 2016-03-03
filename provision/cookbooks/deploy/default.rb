root_path = '/home/homepage/Easy_GoogleCloudVision'
app_path = root_path + '/app'

execute 'install composer package' do
  command "composer install"
  cwd app_path
  user 'homepage'

  only_if 'type composer'
end

execute 'git pull' do
  command 'git pull origin master'
  cwd root_path
  user 'homepage'
end

service 'nginx' do
  action :restart
end

service 'php-fpm' do
  action :restart
end
