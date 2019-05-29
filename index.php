<?php

    class Dolgosrok{
        
        public function CurrencyExchange(){
            
            //валюты
            $currenciesArr = [
                '1'     => ['USA dollar', 'USD', '840'], //доллар США
                '2'     => ['European euro', 'EUR', '978'], //евро
                '3'     => ['Japanese yen', 'JPY', '392'], //японская йена
                '4'     => ['Pound sterling', 'GBP', '826'], //британский фунт
                '5'     => ['Swiss franc', 'CHF', '756'], //швейцарский франк
                '6'     => ['Canadian dollar', 'CAD', '124'], //канадский доллар
                '7'     => ['Australian dollar', 'AUD', '036'], //австралийский доллар
                '8'     => ['New Zealand dollar', 'NZD', '554']  //новозеландский доллар        
            ];

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

            if((strtotime("$year-$month-$day") > strtotime(date("Y-m-d"))) || !checkdate($month, $day, $year)){
                $code .=
                        "<div class=\"error\">
                            <b>Enter a valid date!</b>
                         </div>";
            }

            else{
                $url = "http://cbrates.rbc.ru/tsv/$currency/$year/$month/$day.tsv";
                $data = file_get_contents($url);
                preg_match('/(\d+)(\s)([0-9].+)/', $data, $matches);

                $code .= 
                        "<div class=\"exchange\">
                            <h2> $matches[1] $currencyName = $matches[3] RUR</h2>
                         </div>";
            }

            $code .=       
                        "</body>
                    </html>";

            return $code;
        }
    }