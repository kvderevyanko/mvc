<?php

namespace Core;

/**
 * Основной класс контроллеров
 * Class Controller
 * @package Core
 */
abstract class Controller
{

    /**
     * @param string $name Имя метода
     * @param array $args Аргументы
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            throw new \Exception("Метод $method не найден в контроллере " . get_class($this));
        }
    }
}
