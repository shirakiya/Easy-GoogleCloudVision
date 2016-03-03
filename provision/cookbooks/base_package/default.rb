# 基本必要となるパッケージをインストールする

%w(
  sudo
  which
  gcc
  gcc-c++
  openssl
  openssl-devel
).each do |pkg|
  package pkg
end
