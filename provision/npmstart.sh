#!/bin/bash

sudo systemctl stop apache2
sudo systemctl restart nginx

#start npm
sudo npm start --prefix /var/www/application/public/portfolio_react &