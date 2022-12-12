#!/bin/sh

#install
container_name=generate-document

docker build --tag $container_name:local .
docker run -it --name $container_name -d $container_name:local sh
