<?php
// Thông tin kết nối
$servername = "mysql"; // Tên host của MySQL, thường là tên dịch vụ trong docker-compose
$username = "user"; // Tên người dùng
$password = "123456"; // Mật khẩu
$dbname = "my_database"; // Tên cơ sở dữ liệu

// Kết nối đến MySQL
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully<br>";

// Tạo bảng nếu nó chưa tồn tại
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    email VARCHAR(50),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table users created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error . "<br>";
}

// Chèn dữ liệu vào bảng
$sql = "INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com')";
if ($mysqli->query($sql) === TRUE) {
    echo "New record created successfully<br>";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error . "<br>";
}

// Truy xuất và hiển thị dữ liệu từ bảng
$sql = "SELECT id, name, email, reg_date FROM users";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // Hiển thị dữ liệu
    echo "<table border='1'>
    <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Registration Date</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
        <td>" . $row["id"] . "</td>
        <td>" . $row["name"] . "</td>
        <td>" . $row["email"] . "</td>
        <td>" . $row["reg_date"] . "</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "0 results<br>";
}

// Đóng kết nối
$mysqli->close();
?>
