#!/bin/bash
#

cd /vagrant
phing -f ./provision/build.xml -Denvironment=dev -Dbranch=dev -Dusername=admin -Demail=admin@app.int -Dpassword=admin
