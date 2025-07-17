<?php
// ✅ Database connection
$conn = new mysqli("localhost", "username", "password", "database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ DeepSeek API Configuration
$api_key = "DEEP-SEEK API";
$api_url = "https://api.deepseek.com/v1/chat/completions";

// ✅ Function to get news from DeepSeek
function getNewsFromDeepSeek($api_url, $api_key, $category) {
    $postData = [
        "model" => "deepseek-chat",
        "messages" => [
            ["role" => "system", "content" => "You are a professional $category news writer."],
            ["role" => "user", "content" =>
                "Generate 3 unique and fresh $category news articles. 
                 Each should have a catchy title (first line) followed by 1-2 paragraphs of content. 
                 Focus on current trends in $category. 
                 Make sure they are different from previous ones. Current time: " . date("Y-m-d H:i:s")]
        ],
        "max_tokens" => 800,
        "temperature" => 0.9,
        "top_p" => 0.95
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return false;
    }

    $data = json_decode($response, true);
    return $data["choices"][0]["message"]["content"] ?? false;
}

// ✅ Insert News into Database
if (isset($_POST['insert_news'])) {
    $category = $_POST['category'] ?? "Technology";
    $deepseek_response = getNewsFromDeepSeek($api_url, $api_key, $category);

    if ($deepseek_response) {
        $articles = preg_split("/\n\s*\n/", trim($deepseek_response)); // Split by blank lines
        $inserted = 0;
        $skipped = 0;

        foreach ($articles as $article) {
            $lines = explode("\n", trim($article));
            $title = trim($lines[0]);
            $content = trim(implode("\n", array_slice($lines, 1)));

            if (empty($title) || empty($content)) continue;

            // ✅ Check for duplicate title
            $check_stmt = $conn->prepare("SELECT id FROM gonews WHERE title = ?");
            $check_stmt->bind_param("s", $title);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows == 0) {
                $slug = strtolower(str_replace(" ", "-", preg_replace("/[^a-zA-Z0-9 ]/", "", $title))) . "-" . time();
                $author_id = 1;
                $featured_image = "assets/card.jpeg";
                $is_top = rand(0, 1);
                $status = "published";

                $stmt = $conn->prepare("INSERT INTO gonews (category, title, slug, content, author_id, featured_image, is_top, status) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssisis", $category, $title, $slug, $content, $author_id, $featured_image, $is_top, $status);

                if ($stmt->execute()) {
                    $inserted++;
                }
                $stmt->close();
            } else {
                $skipped++;
            }

            $check_stmt->close();
        }

        $message = "✅ [$category] Inserted: $inserted new article(s). Skipped: $skipped duplicate(s).";
    } else {
        $message = "❌ Failed to fetch $category news from DeepSeek.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Category News Fetcher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Helvetica", "Arial", sans-serif; background-color: #f8f9fa; color: #000; }
        .header, .footer {
            background: rgba(33, 45, 69, 0.9); color: #fff; text-align: center; padding: 15px 0;
            border-radius: 0 0 12px 12px;
        }
        .footer { border-radius: 12px 12px 0 0; margin-top: 30px; }
        .card {
            max-width: 500px; margin: auto; border-radius: 15px; background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .btn-custom {
            background-color: #ffc03d; color: #000; font-weight: bold; border: none;
            border-radius: 8px; transition: 0.3s;
        }
        .btn-custom:hover { background-color: #e6aa32; }
    </style>
</head>
<body>

<div class="header">
    <h2>📰 GoNews Admin - Multi-Category News Fetcher</h2>
</div>

<div class="container mt-5">
    <div class="card p-4">
        <h4 class="mb-3 text-center" style="color:#212d45;">Fetch News by Category</h4>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-info text-center"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="category" class="form-label">Select Category</label>
                <select name="category" class="form-select" required>
                    <option value="Technology">Technology</option>
                    <option value="Politics">Politics</option>
                    <option value="Medicine">Medicine</option>
                    <option value="Business">Business</option>
                    <option value="Education">Education</option>
                </select>
            </div>
            <button type="submit" name="insert_news" class="btn btn-custom w-100">
                Fetch & Insert News
            </button>
        </form>
    </div>
</div>

<div class="footer">
    &copy; <?= date("Y"); ?> GoHybrid - Powered by Technology
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
