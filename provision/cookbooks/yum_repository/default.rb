package 'install epel repository' do
  name 'epel-release'
  not_if 'rpm -q epel-release'
end

package 'install remi repository' do
  name 'http://rpms.famillecollet.com/enterprise/remi-release-6.rpm'
  not_if 'rpm -q remi-release'
end
