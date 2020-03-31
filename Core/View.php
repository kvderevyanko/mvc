<?php

namespace Core;

/**
 * Обработка шаблонов
 * Class View
 * @package Core
 */
class View
{
    /**
     * Рендер вьюшки с использованием Twig
     *
     * @param string $template  Файл шаблона
     * @param array $args  Аргументы для передачи в шаблон
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig\Environment($loader);
        }
        echo $twig->render($template, $args);
    }
}
