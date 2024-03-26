<?php
namespace Db;

class Accounts
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "SELECT * FROM accounts;";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $error) {
            exit ($error->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "SELECT * FROM accounts WHERE AccountID = ?;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result[0]; // Only return the first element of a single result
        } catch (\PDOException $error) {
            exit ($error->getMessage());
        }
    }

    // Note that the validation process happens in the register class
    // This method only inserts the validated data passed by the register class
    public function insert(array $data)
    {
        $statement = "
        INSERT INTO accounts 
            (AccountID, Username, Password, Email, Phone, Name, Gender, Age, Photo, Address, Type)
        VALUES
            (:AccountID, :Username, :Password, :Email, :Phone, :Name, :Gender, :Age, :Photo, :Address, :Type);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    ':AccountID' => $data['AccountID'],
                    ':Username' => $data['Username'],
                    ':Password' => $data['Password'],
                    ':Email' => $data['Email'],
                    ':Phone' => $data['Phone'],
                    ':Name' => $data['Name'],
                    ':Gender' => $data['Gender'],
                    ':Age' => $data['Age'] ?? null,
                    ':Photo' => $data['Photo'] ?? null,
                    ':Address' => $data['Address'],
                    ':Type' => $data['Type']
                )
            );
            return $statement->rowCount();
        } catch (\PDOException $error) {
            exit ($error->getMessage());
        }
    }

    // There are several types of the update process
    // Is is based on the sensitive data of the account attributes
    // Sensitive data: username, email, password, photo
    // Non-sensitive data: name, phone, gender, age, address
    // AccountID and Type are constant attributes that do not need to be changed
    public function update($id, array $data, int $type = 0)
    {
        switch ($type) {
            case 0: // Non sensitive data
                $statement = "
                UPDATE accounts SET Phone = :Phone, Name = :Name, Gender = :Gender, Age = :Age, Address = :Address
                WHERE AccountID = :AccountID;
                ";
                try {
                    $statement = $this->db->prepare($statement);
                    $statement->execute(
                        array(
                            ':AccountID' => $id,
                            ':Phone' => $data['Phone'],
                            ':Name' => $data['Name'],
                            ':Gender' => $data['Gender'],
                            ':Age' => $data['Age'] ?? null,
                            ':Address' => $data['Address']
                        )
                    );
                } catch (\PDOException $error) {
                    exit ($error->getMessage());
                }
                $return = $statement->rowCount();
                break;
            case 1: // Username
                $current_username = ($this->find($id))[0]['Username'];
                $is_exist = false;
                foreach ($this->findAll() as $account) {
                    if (($account['Username'] == $data['Username']) && ($account['Username'] != $current_username)) {
                        $is_exist = true;
                    }
                }
                if (!$is_exist) {
                    $statement = "UPDATE accounts SET Username = :Username WHERE AccountID = :AccountID;";
                    try {
                        $statement = $this->db->prepare($statement);
                        $statement->execute(
                            array(
                                ':AccountID' => $id,
                                ':Username' => $data['Username']
                            )
                        );
                    } catch (\PDOException $error) {
                        exit ($error->getMessage());
                    }
                    $return = $statement->rowCount();
                } else {
                    $return = "Username $data[Username] already exists. Please choose a different username.";
                }
                break;
            case 2: // Email
                break;
            case 3: // Password
                break;
            case 4: // Photo
                break;
        }
        return $return;
    }

    public function delete($id)
    {
        $statement = "DELETE FROM accounts WHERE AccountID = :AccountID;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    ':AccountID' => $id
                )
            );
            return $statement->rowCount();
        } catch (\PDOException $error) {
            exit ($error->getMessage());
        }
    }
}
