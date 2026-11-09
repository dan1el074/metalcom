<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Metalcom | Em manutenção</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100svh;
            display: grid;
            place-items: center;
            padding: 32px 24px;
            background: #141614;
            color: #f5f5f2;
            font-family: "Segoe UI", Arial, sans-serif;
            text-align: center;
        }

        main {
            width: 100%;
            max-width: 460px;
        }

        img {
            display: block;
            width: 300px;
            max-width: 85%;
            height: auto;
            margin: 0 auto 48px;
            filter: invert(1) hue-rotate(180deg);
            mix-blend-mode: screen;
        }

        h1 {
            margin: 0 0 14px;
            font-size: clamp(24px, 5vw, 30px);
            font-weight: 500;
            letter-spacing: -0.02em;
        }

        p {
            margin: 0;
            color: #a7afa7;
            font-size: 16px;
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <main>
        <img src="<?= e(asset('assets/logo-metalcom.png')) ?>"
             alt="Metalcom Marmentini">
        <h1>Site em manutenção</h1>
        <p>Estamos atualizando nosso catálogo.<br>Em breve, estaremos de volta.</p>
    </main>
</body>
</html>
