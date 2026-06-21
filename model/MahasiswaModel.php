<?php

class MahasiswaModel
{
  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function emailExists($email)
  {
    $stmt = $this->conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
  }

  public function createMahasiswa($dataPost, $cvPath, $fotoPath)
  {
    $this->conn->begin_transaction();
    try {
      $stmtUser = $this->conn->prepare("INSERT INTO users (nama, email, password, role, created_at) VALUES (?, ?, ?, 'mahasiswa', NOW())");
      $stmtUser->bind_param('sss', $dataPost['nama'], $dataPost['email'], $dataPost['password']);
      $stmtUser->execute();
      $userId = $stmtUser->insert_id;

      $stmtMhs = $this->conn->prepare('INSERT INTO mahasiswa (user_id, universitas, jurusan, semester, skill, cv_file, foto) VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmtMhs->bind_param('issssss', $userId, $dataPost['universitas'], $dataPost['jurusan'], $dataPost['semester'], $dataPost['skill'], $cvPath, $fotoPath);
      $stmtMhs->execute();

      $this->conn->commit();
      return true;
    } catch (Exception $e) {
      $this->conn->rollback();
      return false;
    }
  }
  // Get Mahasiswa by User ID
  public function getMahasiswaByUserId($userId) {
      $stmt = $this->conn->prepare("SELECT u.nama, u.email, m.id as mahasiswa_id, m.universitas, m.jurusan, m.semester, m.skill, m.cv_file, m.foto FROM users u JOIN mahasiswa m ON u.id = m.user_id WHERE u.id = ?");
      $stmt->bind_param("i", $userId);
      $stmt->execute();
      $result = $stmt->get_result();
      return $result->fetch_assoc();
  }

  // Update Mahasiswa Profile
  public function updateMahasiswa($userId, $dataPost, $cvPath, $fotoPath) {
      $this->conn->begin_transaction();
      try {
          $stmtUser = $this->conn->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
          $stmtUser->bind_param("ssi", $dataPost['nama'], $dataPost['email'], $userId);
          $stmtUser->execute();

          $queryMhs = "UPDATE mahasiswa SET universitas = ?, jurusan = ?, semester = ?, skill = ?";
          $types = "ssss";
          $params = [
              $dataPost['universitas'],
              $dataPost['jurusan'],
              $dataPost['semester'],
              $dataPost['skill']
          ];

          if ($cvPath !== null) {
              $queryMhs .= ", cv_file = ?";
              $types .= "s";
              $params[] = $cvPath;
          }
          if ($fotoPath !== null) {
              $queryMhs .= ", foto = ?";
              $types .= "s";
              $params[] = $fotoPath;
          }
          $queryMhs .= " WHERE user_id = ?";
          $types .= "i";
          $params[] = $userId;

          $stmtMhs = $this->conn->prepare($queryMhs);
          $stmtMhs->bind_param($types, ...$params);
          $stmtMhs->execute();

          $this->conn->commit();
          return true;
      } catch (Exception $e) {
          $this->conn->rollback();
          return false;
      }
  }

  // Delete Mahasiswa
  public function deleteMahasiswa($userId) {
      $this->conn->begin_transaction();
      try {
          $stmtMhs = $this->conn->prepare("DELETE FROM mahasiswa WHERE user_id = ?");
          $stmtMhs->bind_param("i", $userId);
          $stmtMhs->execute();

          $stmtUser = $this->conn->prepare("DELETE FROM users WHERE id = ?");
          $stmtUser->bind_param("i", $userId);
          $stmtUser->execute();

          $this->conn->commit();
          return true;
      } catch (Exception $e) {
          $this->conn->rollback();
          return false;
      }
  }
}
?>