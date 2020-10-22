require 'yaml'

if File.exist?("config.yaml")
    configuration = YAML.load_file("config.yaml")
  else
    raise Vagrant::Errors::VagrantError.new, "Configuration file not found!"
  end

Vagrant.require_version ">= 2.2.6"

Vagrant.configure(2) do |config|
    config.vm.box = "ubuntu/bionic64"
    config.vm.box_check_update = true
    config.vm.network "private_network", ip: configuration["ip"]
    config.vm.provider "virtualbox" do |vb|
        vb.memory = configuration["virtualbox"]["memory"]
        vb.name = configuration["name"]+"dev"
    end

    config.vm.hostname = configuration["hostname"]
    config.vm.network :private_network, ip: configuration["ip"]
    config.vm.network "forwarded_port", guest: 9229, host: 9229
    config.vm.network "forwarded_port", guest: 27017, host: 27017

    config.vm.synced_folder configuration["path"], "/home/vagrant/" + configuration["name"], :mount_options => [ "dmode=777", "fmode=777" ], :owner => 'www-data', :group => 'www-data'

    config.vm.provision "shell", path: "provision/bootstrap.sh", :args => [
    configuration["timezone"],
    configuration["db"]["user"],
    configuration["db"]["password"],
    configuration["db"]["name"],
    configuration["name"]
   ],
   privileged: true
   config.vm.provision :shell, path: "provision/npmstart.sh", run: "always", privileged: true
end