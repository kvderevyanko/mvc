В настройках apache/nginx указываем как корень папку  public

Обновляем composer
    
    composer update

В файле App/Config.php проставляем настройки подключения к базе данных

#### Создаём таблицу в БД

    CREATE TABLE `tasks` (
      `id` int(11) NOT NULL,
      `username` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `text` text NOT NULL,
      `status` tinyint(1) DEFAULT NULL,
      `edited` tinyint(1) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    
    ALTER TABLE `tasks`
      ADD PRIMARY KEY (`id`);
    
    ALTER TABLE `tasks`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;