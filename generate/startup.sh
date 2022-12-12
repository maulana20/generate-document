#!/bin/bash

# imagick
imagemagic_config=/etc/ImageMagick-6/policy.xml
if [ -f $imagemagic_config ]; then
  sed -i 's/<policy domain="coder" rights="none" pattern="PDF" \/>/<policy domain="coder" rights="read|write" pattern="PDF" \/>/g' $imagemagic_config;
else
  echo did not see file $imagemagic_config;
fi

# general
composer install
