<?php
// ajukan-event.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event</title>

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-wFv5wFv6FvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFvFv==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #e4ebf5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            max-width: 520px;
            width: 100%;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            transition: transform 0.2s ease;
        }

        .container:hover {
            transform: translateY(-3px);
        }

        h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
        }

        .info-box {
            padding: 18px 20px;
            background: #e8f0fe;
            border-left: 5px solid #4285f4;
            border-radius: 8px;
            font-size: 15px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 15px;
            transition: all 0.2s ease;
            color: #fff;
        }

        .btn i {
            margin-right: 10px;
            font-size: 18px;
        }

        .btn.wa {
            background: #25D366;
        }

        .btn.wa:hover {
            background: #1ebe5d;
        }

        .btn.ig {
            background: #d6249f;
        }

        .btn.ig:hover {
            background: #c3178d;
        }

        @media(max-width: 600px) {
            .container {
                padding: 25px 20px;
            }
            h2 {
                font-size: 22px;
            }
            .info-box {
                font-size: 14px;
            }
            .btn {
                font-size: 14px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ajukan Event</h2>

        <div class="info-box">
            Untuk memasukkan event ke aplikasi, silakan hubungi admin melalui WhatsApp atau Instagram. Sertakan detail lengkap event dan kode event jika sudah tersedia.
        </div>

        <a href="https://wa.me/6285169047916" class="btn wa" target="_blank">
            <i class="fab fa-whatsapp"></i> Chat Admin via WhatsApp
        </a>
        <a href="https://instagram.com/imeunjee__" class="btn ig" target="_blank">
            <i class="fab fa-instagram"></i> Hubungi via Instagram
        </a>
    </div>
</body>
</html>
