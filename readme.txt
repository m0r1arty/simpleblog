Установка.

Получить пакеты.
bash-4.2$ composer update

Установить миграции
bash-4.2$ ./yii migrate/up
bash-4.2$ ./yii migrate/up --migrationPath modules/blog/migrations
bash-4.2$ ./yii migrate/up --migrationPath modules/sef/migrations
bash-4.2$ ./yii migrate/up --migrationPath modules/grabber/migrations

Создать пользователя:
bash-4.2$ ./yii users/create tester1
Эта команда выдаст что-то вроде:
User "tester1", Password: "DaUJhSFMI1bok5", Token: "E-8eEYNaqpNEUqbMShUbQJy1ZIcXlyX0quQ2qZv_S3pZsEufoLxBSH3SA6a_Kclm"

С учётными данными доступна админская часть. Токен пригодится для rest:
bash-4.2$ curl --user 'E-8eEYNaqpNEUqbMShUbQJy1ZIcXlyX0quQ2qZv_S3pZsEufoLxBSH3SA6a_Kclm:' -i -H "Accept:application/json" -H "Content-type:application/json" -X POST -d '{"title":"rest title","preview":"test preview","content":"rest content","categoryIDs":"1,3"}' "http://blog/records"

*Здесь blog прописан в hosts как 127.0.0.1


Установка начальных данных(можно пропустить) создаст 3 категории в 1ю и 3ю пропишет одну запись. Создаст директории @runtime/grabber/{json,xml}. Установит 3 задачи: граббер новостей с сайта https://chelseablues.ru, граббер записей в формате json из @runtime/grabber/json, граббер записей в формате json из @runtime/grabber/json.
bash-4.2$ ./yii initial-data

После установки queue через крон(или другим воркером):
* * * * * /usr/bin/php /var/www/my_project/yii queue/run

можно на нужное время установить помещение задач в очередь
0 * * * * /usr/bin/php /var/www/my_project/yii grabber/placeall
или
0 * * * * /usr/bin/php /var/www/my_project/yii grabber/place [id]

Задачи можно запускать в том числе вручную через команды:
для запуска конкретной
bash-4.2$ ./yii grabber/run [id]
или для запуска всех
bash-4.2$ ./yii grabber/runall
