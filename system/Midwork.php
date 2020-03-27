<?php

class Midwork
{
    public $conn;

    public function __construct()
    {
        if (config('db_user')) {
            try {
                $dsn = 'mysql:';

                if(config('db_host')){
                    $dsn .= "host=" . config('db_host');
                } else {
                    $dsn .= "unix_socket=" . config('db_socket');
                }

                $dsn .= ';dbname=' . config('db_name');

                $this->conn = new PDO($dsn, config('db_user'), config('db_password'));
                // @todo set attributes, err mode?
                $this->conn->query("SET NAMES utf8mb4");
            }
            catch(PDOException $e)
            {
                die('Connection failed' . $e->getMessage());
            }
        }
    }

    public function prepare($prepare)
    {
        return $this->conn->prepare($prepare);
    }

    public function query($query)
    {
        return $this->conn->query($query);
    }

    public function last_id()
    {
        return $this->conn->lastInsertId();
    }

    public function ins($table, $data)
    {
        $columns = implode(',', array_keys($data));

        $questions = '?';
        for($i=1;$i<count($data); $i++) {
            $questions .= ',?';
        }

        $q = $this->prepare("INSERT INTO $table ($columns) VALUES ($questions)");
        $q->execute(array_values($data));
        return $this->last_id();
    }

    public function upd($table, $id, $data)
    {
        $sets = false;
        foreach ($data as $key => $value) {
            $sets .= ', ' . $key . '=?';
        }
        $sets = ltrim($sets, ',');

        if (is_numeric($id)) { //
            $column = 'id';
            $data['id'] = $id;
        } else {
            foreach ($id as $key => $value) {
                $column = $key;
                $data['id'] = $value;
            }
        }

        $q = $this->prepare("UPDATE $table SET $sets WHERE $column = ?");
        $q->execute(array_values($data));
        //print_r($id);
    }

    public function get($table, $id)  // @todo
    {
        if (is_numeric($id)) { // !is_array
            $q = $this->prepare("SELECT * FROM $table WHERE id = ?");
            $q->execute([$id]);
        } elseif ($id == 'rand') {
            $q = $this->query("SELECT * FROM $table ORDER BY RAND()");
        } else {
            $column = array_keys($id)[0];

            $q = $this->prepare("SELECT * FROM $table WHERE $column = ?");
            $q->execute(array_values($id));
        }
        return $q->fetch();
    }

    public function get_all($table, $where = false, $order_by = false)
    {
        // intro
        $sql = "SELECT * FROM $table";

        // adding where
        if ($where) {
            $column = array_keys($where)[0];
            $sql .= " WHERE $column = ?";
        }

        // prepare
        $q = $this->prepare($sql);

        // execute
        if ($where) {
            $value = array_values($where)[0]; // simplify
            $q->execute([$value]);
        } else {
            $q->execute();
        }

        return $q->fetchAll();
    }

    public function del($table, $id)
    {
        if (is_numeric($id)) { // !is_array
            $q = $this->prepare("DELETE FROM $table WHERE id = ?");
            $q->execute([$id]);
        } else {
            $column = array_keys($id)[0];
            $value = array_values($id)[0];

            $q = $this->prepare("DELETE FROM $table WHERE $column = ?");
            $q->execute([$value]);
        }

    }

    protected function validate($name, $rules)
    {
        $_SESSION['post'] = $_POST;

        $rules = explode('|', $rules);
        foreach ($rules as $rule) {
            if ($rule == 'required') {
                if (empty($_POST[$name])) {
                    alert('The <span class="alert-link">' . ucfirst($name) . '</span> is required', 'danger');
                    back();
                }
            }elseif ($rule == 'email') {
                if (!filter_var($_POST[$name], FILTER_VALIDATE_EMAIL)) {
                    alert('The <span class="alert-link">' . ucfirst($name) . '</span> must contain correct email, e.g. user@domain.com', 'danger');
                    back();
                }
            }elseif (preg_match('/min:([0-9]+)/', $rule, $output)) {
                if (strlen($_POST[$name]) < $output[1]) {
                    alert('The <span class="alert-link">' . ucfirst($name) . '</span> must be at least ' . $output[1] . ' characters', 'danger');
                    back();
                }
            }elseif (preg_match('/max:([0-9]+)/', $rule, $output)) {
                if (strlen($_POST[$name]) > $output[1]) {
                    alert('The <span class="alert-link">' . ucfirst($name) . '</span> must not contain more than ' . $output[1] . ' characters', 'danger');
                    back();
                }
            }elseif (preg_match('/unique:([a-z_]+)/i', $rule, $output)) {
                if ($this->get($output[1], [$name => $_POST[$name]])) {
                    alert('This <span class="alert-link">' . ucfirst($name) . '</span> already exists', 'danger');
                    back();
                }
            }
        }
    }

    public function bb($message) {
        $message = preg_replace_callback('/\[f([0-9]{1,3})\]/',
            function ($matches) {
                $file = $this->get('files', $matches[1]);
                if (!$file) {
                    return '[image-' . $matches[1] . '-not-found]';
                }
                return '<a href="/public/upload/' . $file['name'] . '"><img src="/public/upload/' . $file['name'] . '" style="max-height: 250px;" class="card-img-top" alt="..."></a>';
            },
            $message
        );
        $message = preg_replace_callback('/\[y=([a-zA-Z0-9_-]{11})]/',
            function ($matches) {
                return '<div class="text-center"><iframe width="95%" height="370" src="https://www.youtube.com/embed/' . $matches[1] . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
            },
            $message
        );



        /*$line = preg_replace_callback(
            '|<p>\s*\w|',
            function ($matches) {
                return strtolower($matches[0]);
            },
            $line
        );
        echo $line;*/


        return nl2br($message);
    }
}