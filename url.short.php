<?php

// DB 연결 정보
$conn = new mysqli("localhost", "aass6863", "1406", "mydatabase");

// 연결 확인
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_url = $_POST['address'];
    $new_url = generateShortUrl($old_url); // URL을 전달하여 해시값 생성

    $sql = "INSERT INTO urltable (old_url, new_url, create_date) VALUES ('$old_url', '$new_url', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "주소가 성공적으로 저장되었습니다. 새로운 주소: <a href='url_short.php?url=$new_url' target='_blank'>http://localhost/url_short.php?url=$new_url</a>";
    } else {
        echo "주소 저장 실패: " . $conn->error;
    }
}

// 해시값 생성 함수
function generateShortUrl($url) {
    return substr(hash('sha256', $url), 0, 6);
}

// 리다이렉트 처리
if (isset($_GET['url'])) {
    $result = $conn->query("SELECT old_url FROM urltable WHERE new_url = '" . $_GET['url'] . "'");

    if ($result && $result->num_rows > 0) {
        header("Location: " . $result->fetch_assoc()['old_url']);
        exit();
    } else {
        echo "해당 URL이 존재하지 않습니다.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>긴 주소 짧게 변환</title>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="address">변환할 주소:</label>
        <input type="text" id="address" name="address" placeholder="주소를 입력하세요">
        <button type="submit">short link로 변환</button>
    </form>
</body>
</html>
