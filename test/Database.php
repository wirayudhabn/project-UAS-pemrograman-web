<?php
// File: Database.php
class Database {
    private $host = "localhost";
    private $user = "root"; // Sesuaikan dengan user Laragon kamu
    private $pass = "";     // Sesuaikan dengan password Laragon kamu
    private $db_name = "magang"; // Ganti dengan nama database kalian
    protected $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db_name);
        
        if ($this->conn->connect_error) {
            die("Koneksi Database Gagal: " . $this->conn->connect_error);
        }
    }
}
?>