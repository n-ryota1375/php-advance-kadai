<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = 'root';

// submitパラメータの値が存在するとき（「登録」ボタンを押したとき）の処理
if (isset($_POST['submit'])) {
    try {
        $pdo = new PDO($dsn, $user, $password);

        // 動的に変わる値をプレースホルダに置き換えたINSERT文をあらかじめ用意する
        $sql_insert = '
            INSERT INTO books (book_code, book_name, price, stock_quantity, genre_code)
            VALUES (:book_code, :book_name, :price, :stock_quantity, :genre_code)
        ';
        $stmt_insert = $pdo->prepare($sql_insert);

        //実際の値を割り当てる
        $stmt_insert->bindValue(':book_code', $_POST['book_code'], PDO::PARAM_INT);
        $stmt_insert->bindValue(':book_name', $_POST['book_name'], PDO::PARAM_STR);
        $stmt_insert->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
        $stmt_insert->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
        $stmt_insert->bindValue(':genre_code', $_POST['genre_code'], PDO::PARAM_INT);

        //実行
        $stmt_insert->execute();

        //追加した件数を取得
        $count = $stmt_insert->rowCount();

        $message = "書籍を{$count}件取得しました。";

        //一覧ページにリダイレクト
        header("Location: read.php?message={$message}");   
    } catch (PDOException $e) {
        exit ($e->getMessage());
    }
}

try {
    $pdo = new PDO($dsn, $user, $password);

    //SQL文の作成
    $sql_select = 'SELECT genre_code FROM genres';

    //実行
    $stmt_select = $pdo->query($sql_select);

    //結果を配列で取得
    $genre_codes = $stmt_select->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    exit($e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="ja">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍管理アプリ</title>
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fontsの読み込み -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">        
</head>

<body>
    <header>
        <nav>
            <a href="index.php">書籍管理アプリ</a>
        </nav>
    </header>
    <main>
        <article class="registration">
            <h1>書籍登録</h1>
            <div class="back">
            <a href="read.php" class="btn">&lt; 戻る</a>
            </div>
            <form action="create.php" method="post" class="registration-form">
                <div>
                    <label for="book_code">書籍コード</label>
                    <input type="number" name="book_code" min="0" max="100000000" required>

                    <label for="book_name">書籍名</label>
                    <input type="text" name="book_name" maxlength="50" required>

                    <label for="price">単価</label>
                    <input type="number" name="price" min="0" max="100000000" required>

                    <label for="stock_quantity">在庫数</label>
                    <input type="number" name="stock_quantity" min="0" max="100000000" required>

                    <label for="genre_code">ジャンルコード</label>
                    <select name="genre_code" required>
                        <option disabled selected value>選択してください</option>
                        <?php
                        foreach ($genre_codes as $genre_code) {
                            echo "<option value='{$genre_code}'>{$genre_code}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn" name="submit" value="create">登録</button>
            </form>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 商品管理アプリ All rights reserved.</p>
    </footer>
</body>

</html>