#!/bin/sh

#generate
container_name=generate-document
doc=document.docx
final=document-final.pdf

docker cp $doc $container_name:/app
docker exec -it $container_name php run.php $doc $final
docker cp $container_name:/app/$final .
docker exec -it $container_name rm -rf /app/$final
