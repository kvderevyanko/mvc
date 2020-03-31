<?php

namespace Core;

/**
 * Обработка маршрутов
 * Class Router
 * @package Core
 */
class Router
{

    /**
     * Маршруты таблицы маршрутизации
     * @var array
     */
    protected $routes = [];

    /**
     * Параметры из маршрута
     * @var array
     */
    protected $params = [];

    /**
     * Добавление маршрута в таблицу маршрутизации
     *
     * @param string $route URL маршрута
     * @param array $params Параметры (контроллер, action)
     *
     * @return void
     */
    public function add($route, $params = [])
    {
        $route = preg_replace('/\//', '\\/', $route);

        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Получение всех маршрутов из таблицы маршрутизации
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Сопоставление маршрута с маршрутами в таблице маршрутизации
     *
     * @param string $url URL маршрута
     * @return boolean
     */
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Получение текущих параметров
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Обработка маршрута
     *
     * @param string $url URL маршрута
     * @return void
     * @throws \Exception
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (preg_match('/action$/i', $action) == 0) {
                    $controller_object->$action();

                } else {
                    throw new \Exception("Метод $action в контроллере $controller не может быть вызван напрямую - 
                        удалите суффикс Action для вызова этого метода");
                }
            } else {
                throw new \Exception("Класс контроллера $controller не найден");
            }
        } else {
            throw new \Exception('Маршрут не найден.', 404);
        }
    }

    /**
     * Преобразование строки с дефисами  в camelCase
     *
     * @param string $string Строка для конвертации
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Преобразование строки в camelCase (tasks => Tasks)
     *
     * @param string $string Строка для преобразования
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    /**
     * Удаление переменных из URL
     *
     * @param string $url Полный URL
     * @return string Урл с удалёнными переменными
     */
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    /**
     * Получение неймспейса контроллера
     *
     * @return string URL запроса
     */
    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }
}
