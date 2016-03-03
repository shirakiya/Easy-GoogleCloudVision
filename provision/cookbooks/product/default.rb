root_path = '/home/homepage/Easy_GoogleCloudVision'
app_path = root_path + '/app'

git root_path do
  repository 'git@github.com:shirakiya/Easy-GoogleCloudVision.git'
  user 'homepage'

  not_if "test -e #{root_path}"
end

execute 'update composer and install composer package' do
  command "composer self-update && composer install"
  cwd app_path
  user 'homepage'

  only_if 'type composer'
end

execute 'be writable' do
  command "chmod 777 #{app_path + '/images'}"
  user 'root'
end
