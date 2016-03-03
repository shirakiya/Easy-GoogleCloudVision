conf_dir_path = '/etc/nginx/conf.d'
nginx_conf_path = '/etc/nginx/nginx.conf'
product_conf_path = '/etc/nginx/conf.d/' + 'easy_cloudvision.conf'


package 'install nginx repository' do
  name 'http://nginx.org/packages/centos/6/noarch/RPMS/nginx-release-centos-6-0.el6.ngx.noarch.rpm'

  not_if 'rpm -q nginx-release-centos'
end

package 'nginx' do
  action :install
  options '--enablerepo=nginx'
end

log_dir = '/var/log/nginx'
directory log_dir do
  action :create
  mode '0755'
  owner 'nginx'
  group 'root'
end

# move /etc/nginx/conf.d/*.conf for backup
%W(
  #{conf_dir_path + '/default.conf'}
  #{conf_dir_path + '/example_ssl.conf'}
  #{nginx_conf_path}
).each do |f|
  execute f do
    command "mv #{f} #{f + '.orig'}"
    not_if "test -e #{f + '.orig'}"
  end
end

template nginx_conf_path
template product_conf_path

service 'nginx' do
  action [:start, :enable]
end
