<!DOCTYPE html>
<html>
<head>
    <title>Admin </title>
    <!-- Tambahkan link ke Bootstrap CSS di sini -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  </head>
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-center">Admin </h3>

        <?php
      $host = "localhost";
      $username = "noname";
      $password = "210925";
      $database = "ultah";
  
      $conn = new mysqli($host, $username, $password, $database);
  
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
  
      // Fungsi untuk menghindari serangan SQL Injection
      function sanitize($data) {
          global $conn;
          $data = trim($data);
          $data = mysqli_real_escape_string($conn, $data);
          return $data;
      }
  
      if (isset($_POST['tambah'])) {
          $nama = sanitize($_POST['nama']);
          $pesan = sanitize($_POST['pesan']);  
          
              // Pemeriksaan apakah nama sudah ada dalam database
              $checkQuery = "SELECT nama FROM pesan WHERE nama = '$nama'";
              $checkResult = $conn->query($checkQuery);
  
              if ($checkResult->num_rows > 0) {
                  echo "<script>alert('Nama sudah ada dalam database, pesan tidak bisa ditambahkan.'); window.location.href = 'index.php';</script>";
              } else {
                  // Proses unggah foto dengan nama acak
                  $foto_name = uniqid() . "_" . $_FILES['foto']['name'];
                  $foto_tmp = $_FILES['foto']['tmp_name'];
                  $foto_path = "uploads/" . $foto_name;
                  move_uploaded_file($foto_tmp, $foto_path);
  
                  $query = "INSERT INTO pesan (nama, pesan, foto) VALUES ('$nama', '$pesan', '$foto_path')";
                  if ($conn->query($query) === true) {
                      echo "<script>alert('Pesan berhasil dikirim.'); window.location.href = 'admin.php';</script>";
                  } else {
                      echo "<script>alert('Error: Pesan gagal dikirim.'); window.location.href = 'admin.php';</script>";
                  }
              }
          }

          if(isset($_POST['update'])){
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $pesan = $_POST['pesan'];

            // $sql = "SELECT foto FROM pesan WHERE id_pesan='$id'";
            // $result = $conn->query($sql);
            // $row = $result->fetch_assoc();
            $existingFoto = $_POST['foto_lama'];
    
            $foto_name = $existingFoto; // Default value for foto_name
            if (!empty($_FILES['foto']['name'])) {
                $foto_name = uniqid() . "_" . $_FILES['foto']['name'];
                $foto_tmp = $_FILES['foto']['tmp_name'];
                $foto_path = "uploads/" . $foto_name;
                move_uploaded_file($foto_tmp, $foto_path);
    
                // Hapus foto lama jika ada
                if ($existingFoto != "") {
                    unlink($existingFoto);
                }
            }else{
                $foto_path = $existingFoto; 
            }
            $sql = "UPDATE pesan SET nama='$nama', pesan='$pesan', foto='$foto_path' WHERE id_pesan='$id'";
            if ($conn->query($sql) === true) {
                echo "<script>alert('Pesan berhasil diubah.'); window.location.href = 'admin.php';</script>";
            } else {
                echo "<script>alert('Error: Pesan gagal diubah.'); window.location.href = 'admin.php';</script>";
            }
    
        }
       if(isset($_POST['delete']))    {
              $id = $_POST['id'];
              $sql = "DELETE FROM pesan WHERE id_pesan='$id'";
              $conn->query($sql);
          }

        $sql = "SELECT * FROM pesan";
        $result = $conn->query($sql);

        $results_per_page = 5; // Jumlah data per halaman
        $number_of_results = $result->num_rows;
        $number_of_pages = ceil($number_of_results / $results_per_page);

        if (!isset($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        $this_page_first_result = ($page - 1) * $results_per_page;
        $sql .= " LIMIT " . $this_page_first_result . "," . $results_per_page;
        $result = $conn->query($sql);
        ?>

        <!-- Tampilkan data pesan -->
        <div class="container-md">
        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal1">
  Tambah data
</button>
        
        <div class="card p-3 m-1">
        <?php if ($result->num_rows > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Pesan</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_pesan']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['pesan']; ?></td>
                    <td><img src="<?php echo $row['foto']; ?>" width="100"></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id_pesan']; ?>">Edit</button>
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="operation" value="delete">
                            <input type="hidden" name="id" value="<?php echo $row['id_pesan']; ?>">
                            <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?php echo $row['id_pesan']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id_pesan']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?php echo $row['id_pesan']; ?>">Edit Pesan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $row['id_pesan']; ?>">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama:</label>
                                        <input type="text" class="form-control" name="nama" value="<?php echo $row['nama']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pesan" class="form-label">Pesan:</label>
                                        <input type="text" class="form-control" name="pesan" value="<?php echo $row['pesan']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="foto" class="form-label">Foto:</label>
                                        <input type="hidden" name="foto_lama" value="<?php echo $row['foto']; ?>">
                                        <input type="file" class="form-control-file" name="foto">
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Tidak ada data pesan.</p>
<?php endif; ?>


            <!-- Navigasi halaman -->
            <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php for ($page = 1; $page <= $number_of_pages; $page++) : ?>
            <li class="page-item <?php if (isset($_GET['page']) && $page == $_GET['page']) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

        </div>
        

<!-- Modal -->
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah data</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form action="" method="post" enctype="multipart/form-data">
              <input type="hidden" name="operation" value="insert">
              <div class="form-group">
                  <label for="nama">Nama:</label>
                  <input type="text" class="form-control" name="nama" required>
              </div>
              <div class="form-group">
                  <label for="pesan">Pesan:</label>
                  <input type="text" class="form-control" name="pesan" required>
              </div>
              <div class="form-group">
                  <label for="foto">Foto:</label>
                  <input type="file" class="form-control-file" name="foto">
              </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary" name="tambah">Tambah Pesan</button>
            </form>
          
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>


        <!-- Form input data -->
        <!-- <div class="mt-4">
            <h2>Tambah Pesan</h2>
        </div>
    </div> -->

    <!-- Tambahkan link ke Bootstrap JS di sini -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script></body>
</html>
