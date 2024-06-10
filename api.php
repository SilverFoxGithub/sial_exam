<?php
    header('Content-Type:application/json');

    function connect() {
        $host = 'localhost';
        $db = 'sial_exam_db';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

        return $pdo;
    }

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['question_text'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $data['correct_option'])) {
            $pdo = connect();
            $stmt = $pdo->prepare('INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$data['question_text'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $data['correct_option']]);
            echo json_encode(['message' => 'Question added successfully']);
        }

    } elseif ($method === 'GET') {
        $pdo = connect();
        $stmt = $pdo->query('SELECT * FROM questions');
        $questions = $stmt->fetchAll();
        echo json_encode($questions);

    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id'], $data['question_text'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $data['correct_option'])) {
            $pdo = connect();
            $stmt = $pdo->prepare('UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ?');
            $stmt->execute([$data['question_text'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $data['correct_option'], $data['id']]);
            echo json_encode(['message' => 'Question updated successfully']);
        } else {
            echo json_encode(['message' => 'Invalid input']);
        }
    } elseif ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id'])) {
            $pdo = connect();
            $stmt = $pdo->prepare('DELETE FROM questions WHERE id = ?');
            $stmt->execute([$data['id']]);
            echo json_encode(['message' => 'Question deleted successfully']);
        } else {
            echo json_encode(['message' => 'Invalid input']);
        }
    } else {
        echo json_encode(['message' => 'Unsupported request method']);
    }
?>