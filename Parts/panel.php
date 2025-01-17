<?php require_once '../auth.php'; ?>

<?php

require_once("../dbconnect.php");

?>
<!DOCTYPE html>
<html>

<head lang="PL">
<title>Parts</title>
<meta charset ="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="../static/toastr.min.css">
<link rel="shortcut icon" href="../static/clipboard-data.svg">
<script src="../static/jquery.min.js"></script>
<script src="../static/jquery-ui.min.js"></script>
<script src="../static/toastr.min.js"></script>
<script src="../static/jquery-3.6.0.min.js"></script>
<script src="../static/chart.js"></script>
<script src="../static/popper.min.js"></script>
<script src="../static/chosen.jquery.min.js"></script>
<link href="../static/chosen.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="../assets/css/plugins.min.css"/>
<link rel="stylesheet" href="../assets/css/kaiadmin.min.css"/>
    <meta charset="utf-8" />
    <style>
        .img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-width: 60%; /* Dostosowanie do szerokości kontenera */
            height: auto; /* Zachowanie proporcji */
        }
        #number {
            width: 100%;
            font-size: 24px;
            padding: 15px;
            margin-bottom: 20px;
            margin-top: 100px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        #number:focus {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            width: 100%;
            margin-bottom: 10px;
        }
        .button {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 150px;
            font-size: 24px;
            font-weight: bold;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .button:active {
            transform: scale(0.95);
            background-color: #f0f0f0;
        }
        .button.red {
            color: red;
        }
        .button.green {
            color: green;
        }
    </style>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
<div class="container-fluid" style="width:90%; margin: 0 auto;">
    <br />
     <img
            src="../assets/img/logo.png"
            alt="Logo"
            class="img"
            />
    <form id="numberForm" onsubmit="handleSubmit(event)">
    <input type="tel" id="number" class="form-control" placeholder="Wpisz numer" autofocus autocomplete="off"/>
        <div class="grid">
            <div class="button" onclick="appendNumber('1')">1</div>
            <div class="button" onclick="appendNumber('2')">2</div>
            <div class="button" onclick="appendNumber('3')">3</div>
            <div class="button red" onclick="deleteNumber()">X</div>
        </div>
        <div class="grid">
            <div class="button" onclick="appendNumber('4')">4</div>
            <div class="button" onclick="appendNumber('5')">5</div>
            <div class="button" onclick="appendNumber('6')">6</div>
            <div class="button" onclick="appendNumber('0')">0</div>
        </div>
        <div class="grid">
            <div class="button" onclick="appendNumber('7')">7</div>
            <div class="button" onclick="appendNumber('8')">8</div>
            <div class="button" onclick="appendNumber('9')">9</div>
            <div class="button green" onclick="handleSubmit(event)">→</div>
        </div>
    </form>

    <script>

        const input = document.getElementById('number');

        // Lista placeholderów w różnych językach
        const placeholders = [
            { lang: 'pl', text: 'Wpisz numer' },   // Polski
            { lang: 'en', text: 'Enter number' },  // Angielski
            { lang: 'uk', text: 'Введіть номер' }, // Ukraiński
            { lang: 'de', text: 'Nummer eingeben' } // Niemiecki
        ];

        let currentIndex = 0; // Aktualny indeks języka

        // Funkcja zmieniająca placeholder
        function changePlaceholder() {
            currentIndex = (currentIndex + 1) % placeholders.length; // Przechodzenie do następnego języka
            input.placeholder = placeholders[currentIndex].text;
        }

        // Uruchamianie zmiany co 3 sekundy
        setInterval(changePlaceholder, 1500);

        // Automatyczne ustawianie focusu na polu input przy każdym kliknięciu na stronie
        document.body.addEventListener('click', () => {
            input.focus();
        });

        function appendNumber(num) {
            input.value += num;
        }

        function deleteNumber() {
            input.value = '';
        }

        function handleSubmit(event) {
    event.preventDefault(); // Zapobiega przeładowaniu strony
    const userNumber = input.value; // Pobierz wartość z inputa
    if (userNumber.trim() === '') {
        console.log('Pole numeru jest puste!');
        return;
    }
    sendForm(userNumber); // Wywołaj funkcję z wartością inputa
}
        function sendForm(userNumber) {
                    $.ajax({
                        url: 'check_person.php',
                        type: 'POST',
                        data: {
                            number: userNumber
                        },
                        success: function(response) {
                            var czesci = response.split(",")
                            console.log(response);
                            if (czesci[0] === 'true') {
                                console.log('Twój numer znajduje się w bazie danych!');
                                localStorage.setItem('number1', userNumber);
                                localStorage.setItem('nazwa', czesci[1]);
                                localStorage.setItem('czas', 0);
                                window.location.href = 'main.php';
                            } else {
                                console.log('Twój numer nie został odnaleziony w bazie danych.');
                                location.reload();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Wystąpił błąd podczas sprawdzania numeru w bazie danych.');
                            console.log(jqXHR.responseText);
                        },
                    });
                }
    </script>
</html>