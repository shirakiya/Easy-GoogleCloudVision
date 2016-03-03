ssh_dir = '/home/homepage/.ssh'
sshconfig_path = ssh_dir + '/config'
key_path = ssh_dir + '/provision_rsa'
gitconfig_path = '/home/homepage/.gitconfig'

package 'git' do
  action :install
  not_if 'git'
end

directory ssh_dir do
  action :create
  mode '0700'
  owner 'homepage'
  group 'users'
end

template sshconfig_path do
  action :create
  mode '0644'
  owner 'homepage'
  group 'users'
  variables({'key_path' => key_path})
end

# 秘密鍵の配置
remote_file key_path do
  source '~/.ssh/provision_rsa'
  mode '600'
  owner 'homepage'
  group 'users'
end

template gitconfig_path do
  action :create
  mode '0644'
  owner 'homepage'
  group 'users'
end
