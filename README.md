# API
 
ln -s /var/www/start/api/public/images /var/www/start/ui/static


openssl

    openssl genrsa -out id_rsa_jwt.pem 2048
    openssl rsa -in id_rsa_jwt.pem -pubout > id_rsa_jwt.pub
