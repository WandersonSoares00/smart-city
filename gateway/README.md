## Rodar a aplicação

composer install

cd ..

protoc --php_out=gateway/src/protobuf protobuf/messages.proto

mv .env.example .env

php src/run.php
