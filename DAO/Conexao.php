<?php
namespace PHP\Modelo\DAO;

class Conexao {
    private $conn;

    public function conectar() {
        if (!$this->conn) {
            $this->conn = mysqli_connect('localhost', 'root', '', 'Snack');
            if (!$this->conn) {
                throw new Exception("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
            }
        }
        return $this->conn;
    }
}
?>