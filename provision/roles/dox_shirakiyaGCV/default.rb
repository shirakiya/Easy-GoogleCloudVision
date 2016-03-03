%w(
  user
  yum_repository
  base_package
  git
  nginx
  php56
  product
).each do |cb|
  include_cookbook cb
end
