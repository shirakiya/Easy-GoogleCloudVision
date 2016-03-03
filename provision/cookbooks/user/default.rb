username = 'homepage'

user username do
  action :create
  username username
  gid 'users'
  password '$1$JwjcepjW$8phQAlwBHVQTVK949Ws100'
  shell '/bin/bash'
  home "/home/#{username}"
  create_home true

  not_if "cat /etc/passwd | grep #{username}"
end
