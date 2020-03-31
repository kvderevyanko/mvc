<?php

namespace App\Models;

use PDO;

/**
 * Класс по работе с задачами
 * Class Task
 * @package App\Models
 */
class Task extends \Core\Model
{
    /**
     * Количество задач на странице
     * @var integer
     */
    const PER_PAGE = 3;

    /**
     * Получение списка задач
     * @param integer $page Текущая страница
     * @param string $order Поле для сортировки
     * @param string $sort Тип сортировки
     * @return array
     */
    public static function getTasks($page, $order, $sort)
    {
        if($page)
            $page -=1;

        $db = static::getDB();
        $sql = 'SELECT * FROM tasks ORDER BY '.$order.' '.$sort.' LIMIT '.self::PER_PAGE.' OFFSET '.($page * self::PER_PAGE);
        $query = $db->query($sql);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получение общего количества задач
     * @return integer
     */
    public static function countTasks(){
        $db = static::getDB();
        return $db->query('SELECT COUNT(*) FROM tasks')->fetchColumn();
    }

    /**
     * Создание новой задачи
     * @param array $request  параметры из формы
     * @return bool
     */
    public static function createTask($request){
        $username = array_key_exists('username', $request)?trim($request['username']):'';
        $email = array_key_exists('email', $request)?trim($request['email']):'';
        $text = array_key_exists('text', $request)?trim($request['text']):'';

        if($username && $email && $text) {
            $db = static::getDB();
            $sql='INSERT INTO tasks (username, email, text) VALUES (:username, :email, :text)';
            $query=$db->prepare($sql);
            $query->bindParam(':username', $username);
            $query->bindParam(':email', $email);
            $query->bindParam(':text', $text);
            return $query->execute();
        }

        return false;
    }

    /**
     * Получение задачи
     * @param integer $id id задачи
     * @return array|bool
     */
    public static function getTask($id){
        $db = static::getDB();
        $sql = 'SELECT * FROM tasks WHERE id='.$id;
        $query=$db->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Изменение задачи
     * @param integer $id id задачи
     * @param array $request параметры из формы
     * @return bool
     */
    public static function updateTask($id, $request){
        $task = self::getTask($id);
        if($task) {
            $text = array_key_exists('text', $request)?trim($request['text']):'';
            $status = array_key_exists('status', $request)?(int)trim($request['status']):'';
            $edited = 0;
            if($task['edited'] || $task['text'] != $text)
                $edited = 1;

            $db = static::getDB();
            $sql='UPDATE tasks SET text=:text, status=:status, edited=:edited WHERE id='.$id;
            $query=$db->prepare($sql);
            $query->bindParam(':text', $text);
            $query->bindParam(':status', $status);
            $query->bindParam(':edited', $edited);
            return $query->execute();
        }

        return  false;
    }
}
