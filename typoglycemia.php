<?php
function typoglycemia($text) {
    $words = explode(' ', $text);
    $result = [];

    foreach ($words as $word) {
        if (strlen($word) > 3) {
            $middle = str_shuffle(substr($word, 1, -1));
            $word = $word[0] . $middle . $word[strlen($word) - 1];
        }
        $result[] = $word;
    }

    return implode(' ', $result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputText = isset($_POST['text']) ? $_POST['text'] : '';
    $typoglycemiaResult = typoglycemia($inputText);
} else {
    $inputText = '';
    $typoglycemiaResult = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typoglycemia Form</title>
</head>
<body>
    <h1>Typoglycemia Form</h1>
    <form method="post" action="">
        <label for="text">Enter Text:</label>
        <textarea id="text" name="text" rows="4" cols="50"><?php echo htmlspecialchars($inputText); ?></textarea>
        <br>
        <input type="submit" value="Generate Typoglycemia">
    </form>

    <?php if ($typoglycemiaResult): ?>
        <h2>Result:</h2>
        <p id="result"><?php echo htmlspecialchars($typoglycemiaResult); ?></p>
        <button onclick="toggleText()">Toggle Original</button>
    <?php endif; ?>

    <script>
        var originalText = <?php echo json_encode($inputText); ?>;
        var typoglycemiaText = <?php echo json_encode($typoglycemiaResult); ?>;
        var isOriginal = false;

        function toggleText() {
            var resultElement = document.getElementById('result');
            if (isOriginal) {
                resultElement.textContent = typoglycemiaText;
            } else {
                resultElement.textContent = originalText;
            }
            isOriginal = !isOriginal;
        }
    </script>
</body>
</html>
