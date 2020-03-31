<?php

namespace Core;

/**
 * Обработка ошибок
 * Class Error
 * @package Core
 */
class Error
{

    /**
     * Обработчик ошибок. Преобразуйте все ошибки в исключения
     *
     * @param int $level  Уровень ошибки
     * @param string $message  Сообщение об ошибке
     * @param string $file  Имя файла, в котором возникла ошибка
     * @param int $line Номер строки в файле
     * @throws \ErrorException
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Обработчик исключений
     *
     * @param Exception $exception Исключение
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);

        $error = [];
        $error[] = "Исключение: ". get_class($exception);
        $error[] = "Сообщение: ". $exception->getMessage();
        $error[] = "<pre>" . $exception->getTraceAsString() . "</pre>";
        $error[] = "Выброшено в " . $exception->getFile() . " в строке " . $exception->getLine();

        View::renderTemplate("$code.html", ["error" => $error]);
    }
}
