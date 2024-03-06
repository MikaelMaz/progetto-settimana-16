<?php

class UserDTO
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = 'SELECT * FROM users';
        $res = $this->conn->query($sql, PDO::FETCH_ASSOC);

        if ($res) {
            return $res;
        }

        return null;
    }
    public function getUserByID(int $id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $res = $stm->execute(['id' => $id]);

        if ($res) {
            return $res;
        }

        return null;
    }
    public function saveUser(array $user)
    {
        var_dump($user);
        $sql = "INSERT INTO users (firstname, lastname, email, password, isadmin) VALUES (:firstname, :lastname, :email, :password, :isadmin)";
        $stm = $this->conn->prepare($sql);
        $stm->execute(['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'email' => $user['email'], 'password' => $user['password'], 'isadmin' => $user['isadmin']]);
        return $stm->rowCount();
    }
    public function updateUser(array $user)
    {
        try {
            $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, password = :password, isadmin = :isadmin WHERE id = :id";
            $stm = $this->conn->prepare($sql);
            $stm->execute([
                'id' => $user['id'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'email' => $user['email'],
                'password' => $user['password'],
                'isadmin' => $user['isadmin']
            ]);
            return $stm->rowCount();
        } catch (PDOException $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }
    public function deleteUser(int $id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stm = $this->conn->prepare($sql);
        $stm->execute(['id' => $id]);
        return $stm->rowCount();
    }
}