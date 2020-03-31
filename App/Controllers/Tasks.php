<?php

namespace App\Controllers;

use App\Models\Task;
use \Core\View;

class Tasks extends \Core\Controller
{
    /**
     * Список задач
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function indexAction()
    {
        $page = array_key_exists('page', $_GET) ? (int)$_GET['page'] : 1;
        if ($page < 1)
            $page = 1;

        $sort = (array_key_exists('sort', $_GET) && $_GET['sort'] == 'DESC') ? 'DESC' : 'ASC';

        $order = array_key_exists('order', $_GET) ? $_GET['order'] : 'id';

        switch ($order) {
            case 'username':
                break;
            case 'email':
                break;
            case 'text':
                break;
            case 'status':
                break;
            default:
                $order = 'id';
        }

        $tasks = Task::getTasks($page, $order, $sort);
        $totalTasks = Task::countTasks();

        $pages = ceil($totalTasks / Task::PER_PAGE);

        $adminLogin = false;
        if (array_key_exists('admin', $_SESSION) && $_SESSION['admin'])
            $adminLogin = true;

        View::renderTemplate('Tasks/index.html', [
            'title' => 'Список задач',
            'tasks' => $tasks,
            'totalTasks' => $totalTasks,
            'pages' => $pages,
            'page' => $page,
            'order' => $order,
            'sort' => $sort,
            'adminLogin' => $adminLogin
        ]);
    }

    /**
     * Создание задачи
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createAction()
    {
        $error = false;
        $taskCreated = array_key_exists('task_created', $_SESSION) ? $_SESSION['task_created'] : false;
        if ($taskCreated)
            unset($_SESSION['task_created']);

        $post = $_POST;

        if (!$post || !is_array($post))
            $post = [];

        $emailError = false;

        if (array_key_exists('email', $post) && trim($post['email']) && !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $emailError = true;
        }

        if (is_array($post) && count($post) > 0) {
            if (!$emailError && Task::createTask($post)) {
                $_SESSION['task_created'] = true;
                header("Refresh:0");
                exit();
            }
            $error = true;
        }

        View::renderTemplate('Tasks/create.html', [
            'title' => 'Создание задачи',
            'post' => $post,
            'error' => $error,
            'emailError' => $emailError,
            'taskCreated' => $taskCreated
        ]);
    }

    /**
     * Изменение задачи
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function editAction()
    {
        if (!array_key_exists('admin', $_SESSION) || !$_SESSION['admin']) {
            header("Location: /admin/index");
            exit();
        }

        $id = array_key_exists('id', $_GET)?(int)$_GET['id']:'';

        $post = $_POST;

        if (!$post || !is_array($post))
            $post = [];

        if (count($post) > 0) {
            if (Task::updateTask($id, $post)) {
                $_SESSION['task_updated'] = true;
                header("Refresh:0");
                exit();
            }
        }

        $taskUpdated = array_key_exists('task_updated', $_SESSION) ? $_SESSION['task_updated'] : false;
        if ($taskUpdated)
            unset($_SESSION['task_updated']);

        $task = Task::getTask($id);

        View::renderTemplate('Tasks/edit.html', [
            'title' => 'Редактирование задачи',
            'task' => $task,
            'id' => $id,
            'taskUpdated' => $taskUpdated,
        ]);
    }
}
