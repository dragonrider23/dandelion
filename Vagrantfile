# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty32"
  config.vm.network "forwarded_port", guest: 8081, host: 8081
  config.vm.provision :shell, path: "vagrant/vagrant_bootstrap.sh"
end