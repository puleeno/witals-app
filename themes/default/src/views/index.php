<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - PrestoWorld Native</title>
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
            --bg: #0f172a;
            --text: #f8fafc;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
        }
        .container {
            max-width: 800px;
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 24px;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        h1 {
            font-size: 3.5rem;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            font-size: 1.25rem;
            color: #94a3b8;
            margin-bottom: 2rem;
        }
        .posts {
            margin-top: 3rem;
            display: grid;
            gap: 1.5rem;
            text-align: left;
        }
        .post-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 16px;
            transition: transform 0.3s;
            border-left: 4px solid var(--primary);
        }
        .post-card:hover {
            transform: translateX(10px);
            background: rgba(255, 255, 255, 0.08);
        }
        .post-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .post-meta {
            font-size: 0.875rem;
            color: #64748b;
        }
        .badge {
            background: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PrestoWorld Native</h1>
        <p class="subtitle">Trình diễn sức mạnh của Native Theme Engine + CycleORM</p>
        
        <div class="posts">
            <h3>Bài viết mới nhất (Từ WordPress DB):</h3>
            <?php if (!empty($posts_error)): ?>
                <div class="post-card" style="border-left-color: #ef4444;">
                    <div class="post-title" style="color: #ef4444;">Lỗi: <?php echo $posts_error; ?></div>
                    <div class="post-meta">Vui lòng kiểm tra cấu hình Database trong .env hoặc wp-config.php</div>
                </div>
            <?php endif; ?>

            <?php if (empty($posts) && empty($posts_error)): ?>
                <div class="post-card">
                    <div class="post-title">Không tìm thấy bài viết nào.</div>
                </div>
            <?php endif; ?>

            <?php foreach($posts as $post): ?>
                <?php if (is_array($post)): ?>
                <div class="post-card">
                    <div class="post-title"><?php echo $post['title'] ?? 'N/A'; ?> <span class="badge"><?php echo $post['type'] ?? 'post'; ?></span></div>
                    <div class="post-meta">ID: <?php echo $post['id'] ?? '?'; ?> • Ngày đăng: <?php echo $post['date'] ?? 'N/A'; ?></div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
