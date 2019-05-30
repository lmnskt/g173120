<?php

    class CurrencyExchange{
        
        function GetCurrencyExchange(){
            //url с информацией о валютах
            $currencises_url = "http://cbr.ru/eng/currency_base/daily/?date_req=".date("d").".".date("m").".".date("Y");
            $currencises_data = file_get_contents($currencises_url);

            //находим строки
            preg_match_all("/<tr.*?>(.*?)<\/[\s]*tr>/s", $currencises_data, $rows);

            //заполнение массива валют
            for($i = 1; $i < count($rows[1]); ++$i){       
                preg_match_all("/<td.*?>(.*?)<\/[\s]*td>/", $rows[1][$i], $cell);
                $currenciesArr[$i] = [$cell[1][3], $cell[1][1], $cell[1][0]];
            }

            //заполнение значений "по умолчанию"
            isset($_POST["currency"])       ? $currency = $currenciesArr[$_POST["currency"]][2]         : $currency = '840';
            isset($_POST["currency"])       ? $currencyName = $currenciesArr[$_POST["currency"]][1]     : $currencyName = 'USD';
            isset($_POST["day"])            ? $day = $_POST["day"]                                      : $day = date("d");
            isset($_POST["month"])          ? $month = $_POST["month"]                                  : $month = date("m");
            isset($_POST["year"])           ? $year = $_POST["year"]                                    : $year = date("Y");

            $code = "";
            $code .=
                    "<html>
                        <head>
                            <meta charset=\"UTF-8\">
                            <title>Currency exchange</title>
                            <link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\">
                            <link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\" />
                        </head>

                        <body>";

            //выбор валюты
            $code .=
                        "<div class=\"currencyForm\">
                            <h3>Select currency</h3>               
                            <form method=\"post\">
                                <select name=\"currency\" >";
                                    for($i = 1; $i <= count($currenciesArr); ++$i){                               
                                        if($currency == $currenciesArr[$i][2]){
                                            $code .= "<option value=". $i ." selected>".$currenciesArr[$i][0]." (".$currenciesArr[$i][1].")"."</option>";
                                        }

                                        else{
                                            $code .= "<option value=". $i .">".$currenciesArr[$i][0]." (".$currenciesArr[$i][1].")"."</option>";
                                        }
                                    }                         
            $code .=                        
                                "<select>
                        </div>";

            //выбор дня
            $code .=
                        "<div class=\"dateForm\">
                            <h3>Select date</h3>               
                            <form method=\"post\">
                                <select name=\"day\">";
                                    for($i = 1; $i <= 31; ++$i){
                                        if($day == date("d", mktime(0, 0, 0, 1, $i, 2000))){
                                            $code .= '<option value='. date("d", mktime(0, 0, 0, 1, $i, 2000)) .' selected>'. $i .'</option>';
                                        }

                                        else{
                                            $code .= '<option value='. date("d", mktime(0, 0, 0, 1, $i, 2000)) .'>'. $i .'</option>';
                                        }     
                                    }
            $code .=                        
                                "<select>&nbsp;&nbsp;";

            //выбор месяца
            $code .=
                                "<select name=\"month\">";
                                    for($i = 1; $i <= 12; ++$i){
                                        if($month == date("m", mktime(0, 0, 0, $i, 1, 2000))){
                                            $code .= '<option value='. date("m", mktime(0, 0, 0, $i, 1, 2000)) .' selected>'. date("F", mktime(0, 0, 0, $i, 1, 2000)) .'</option>';
                                        }

                                        else{
                                            $code .= '<option value='. date("m", mktime(0, 0, 0, $i, 1, 2000)) .'>'. date("F", mktime(0, 0, 0, $i, 1, 2000)) .'</option>';
                                        }
                                    }
            $code .=                        
                                "<select>&nbsp;&nbsp;";                           

            //выбор года
            $code .=
                                "<select name=\"year\">";
                                    for($i = 2000; $i <= date("Y"); ++$i){
                                        if($year == date("Y", mktime(0, 0, 0, 1, 1, $i))){
                                            $code .= '<option value='. $i .' selected>'. $i .'</option>';
                                        }

                                        else{
                                            $code .= '<option value='. $i .'>'. $i .'</option>';
                                        }
                                    }                            
            $code .=                        
                                "<select>
                                 <input type=\"submit\" value=\"Calculate\">
                        </div>";

            //если введена некорректная дата
            if((strtotime("$year-$month-$day") > strtotime(date("Y-m-d"))) || !checkdate($month, $day, $year)){
                $code .=
                        "<div class=\"error\">
                            <b>Enter a valid date!</b>
                         </div>";
            }

            //если все ок, отображаем курс валют
            else{
                $exchange_url = "http://cbrates.rbc.ru/tsv/$currency/$year/$month/$day.tsv";
                $exchange_data = file_get_contents($exchange_url);
                preg_match('/(\d+)(\s)([0-9].+)/', $exchange_data, $matches);

                $code .= 
                        "<div class=\"exchange\">
                            <h2> $matches[1] $currencyName = $matches[3] RUR</h2>
                         </div>";
            }

            $code .=       
                        "</body>
                    </html>";

            echo $code;
        }
    }