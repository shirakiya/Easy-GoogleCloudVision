## System requirements
- Ruby >= 2.1
- Setup SSH config
	- You can access target servers using SSH.

# How to
1.Install gems

```
$ gem install bundler
$ bundle install --path vendor/bundle
```

# Itamae
for provisioning

```
$ bundle exec -- itamae ssh -u root -h {HostName} -j nodes/provision.json bootstrap.rb
```

for deploy service
```
$ bundle exec -- itamae ssh -u root -h {HostName} -j nodes/provision.json bootstrap.rb
```
