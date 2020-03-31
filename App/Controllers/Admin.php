<?php

namespace App\Controllers;

use \Core\View;

class Admin extends \Core\Controller
{
    /**
     * Логин админа
     * @var string
     */
    const ADMIN_LOGIN = 'admin';

    /**
     * Пароль админа
     * @var string
     */
    const ADMIN_PASSWORD = '123';

    /**
     * Авторизация админа и выхода админа из системы
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function indexAction()
    {
        $post = $_POST;

        if (!$post || !is_array($post))
            $post = [];

        $adminLogin = false;
        if (array_key_exists('admin', $_SESSION) && $_SESSION['admin']) {
            $adminLogin = true;

            if (array_key_exists('logout', $post) && $post['logout']) {
                unset($_SESSION['admin']);
                header("Refresh:0");
                exit();
            }
        }

        if (array_key_exists('username', $post) && array_key_exists('password', $post)) {
            if ($post['username'] == self::ADMIN_LOGIN && $post['password'] == self::ADMIN_PASSWORD) {
                $_SESSION['admin'] = true;
                header("Refresh:0");
                exit();
            }
        }

        View::renderTemplate('Admin/index.html', [
            'title' => 'Авторизация',
            'post' => $post,
            'adminLogin' => $adminLogin
        ]);
    }

}
