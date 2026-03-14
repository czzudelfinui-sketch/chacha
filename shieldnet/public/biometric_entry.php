<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometric Entry - ShieldNet</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: var(--bg-main); overflow: hidden; }
        .bio-container {
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }
        .bio-icon {
            font-size: 80px;
            color: var(--primary-color);
            margin-bottom: 30px;
            cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .bio-icon:active { transform: scale(0.9); }
        .scanning-bar {
            width: 100px;
            height: 4px;
            background: var(--primary-color);
            margin: 0 auto;
            border-radius: 2px;
            position: relative;
            top: -60px;
            opacity: 0;
        }
        .scanning .scanning-bar {
            animation: scan 1.5s infinite;
            opacity: 1;
        }
        @keyframes scan {
            0% { transform: translateY(0); }
            50% { transform: translateY(60px); }
            100% { transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="bio-container">
        <div id="bio-target" class="bio-icon">
            <i class="fas fa-fingerprint"></i>
        </div>
        <div class="scanning-bar"></div>
        <h2 style="color: var(--text-main);">Touch ID to Enter</h2>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 10px;">FaceID / TouchID biometric entry enabled</p>
    </div>

    <script>
        const target = document.getElementById('bio-target');
        const container = document.querySelector('.bio-container');
        
        target.addEventListener('click', () => {
            container.classList.add('scanning');
            setTimeout(() => {
                window.location.href = 'user_dashboard.php';
            }, 1500);
        });
    </script>
</body>
</html>
