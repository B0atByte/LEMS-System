<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden | LEMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            background: #ffffff;
            padding: 60px 50px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 500px;
            border-top: 4px solid #2d2d2d;
        }
        h1 {
            font-size: 90px;
            margin: 0;
            color: #1a1a1a;
            font-weight: 700;
            letter-spacing: -2px;
        }
        h2 {
            margin: 15px 0 10px 0;
            color: #2d2d2d;
            font-weight: 600;
            font-size: 24px;
        }
        p {
            color: #666666;
            margin: 20px 0;
            line-height: 1.6;
            font-size: 15px;
        }
        a {
            display: inline-block;
            padding: 14px 32px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        a:hover {
            background: #2d2d2d;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .error-code {
            display: inline-block;
            padding: 8px 16px;
            background: #f5f5f5;
            border-radius: 4px;
            color: #666666;
            font-size: 13px;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>403</h1>
        <h2>ไม่มีสิทธิ์เข้าถึง</h2>
        <p>ขอโทษครับ คุณไม่มีสิทธิ์เข้าถึงหน้านี้<br>กรุณาติดต่อผู้ดูแลระบบหากคุณคิดว่านี่เป็นข้อผิดพลาด</p>
        <div class="error-code">Error Code: HTTP 403 Forbidden</div>
        <a href="/">กลับหน้าหลัก</a>
    </div>
</body>
</html>
