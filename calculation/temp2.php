
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
<title>Принт-дизайн. Управление ресурсами предприятия</title>
<link href="/pr-diz.ru/css/bootstrap.min.css" rel="stylesheet">
<link href="/pr-diz.ru/fontawesome-free-5.15.1-web/css/all.min.css" rel="stylesheet" />
<link href="/pr-diz.ru/css/jquery-ui.css" rel="stylesheet"/>
<link href="/pr-diz.ru/css/main.css?version=73" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="/pr-diz.ru/favicon.ico" />        <style>
            body {
                padding-left: 0;
                font-family: 'SF Pro Display';
                font-size: 16px;
            }
            
            .header_qr {
                margin-right: 15px;
                height: 80px;
                width: 80px;
            }
            
            .header_qr img {
                height: 80px;
                width: 80px;
            }
            
            .header_title {
                font-size: 18px;
                vertical-align: middle;
            }
            
            .right_logo {
                padding-right: 10px;
            }
            
            #main, #fixed_top {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            #title {
                font-weight: bold;
                font-size: 30px;
                margin-top: 10px;
            }
            
            #subtitle {
                font-weight: bold;
                font-size: 24px;
            }
            
            .topproperty {
                font-size: 18px;
                margin-top: 6px;
            }
            
            .table-header {
                color: #cccccc;
                padding-top: 6px;
                border-bottom: solid 2px gray;
            }
            
            td {
                line-height: 20px;
                padding-top: 7px;
                padding-bottom: 7px;
                border-bottom: solid 1px #cccccc;
            }
            
            tr td:nth-child(2) {
                text-align: right;
                padding-left: 10px;
                font-weight: bold;
            }
            
            tr.left td:nth-child(2) {
                text-align: left;
            }
            
            table.fotometka {
                margin-top: 10px;
                margin-bottom: 10px;
            }
            
            table.fotometka tr td {
                border: solid 1px #dddddd;
                padding-left: 4px;
                padding-top: 4px;
                padding-right: 20px;
                text-align: right;
                vertical-align: top;
            }
            
            td.fotometka img:nth-child(1) {
                 height: 50px;
                 width: auto;
            }
            
            .photolable {
                margin-top: 10px;
                margin-bottom: 10px;
                font-size: 18px;
            }
            
            .border-bottom-2 {
                border-bottom: solid 2px gray;
            }
            
            .printing_title {
                font-size: large;
            }
            
            /*@media print {
                #fixed_top {
                    position: fixed;
                    top: 0px;
                    left: 0px;
                    width: 100%;
                }
                
                #fixed_bottom {
                    position: fixed;
                    bottom: 0px;
                    left: 0px;
                    width: 100%;
                }
                
                #placeholder_top {
                    height: 210px;
                }
                
                .break_page {
                    page-break-before: always;
                    height: 210px;
                }
            }*/
            
            #fixed_bottom table tbody tr td {
                font-size: 18px;
                font-weight: bold;
                height: 50px;
                border: solid 2px #cccccc;
                padding-left: 5px;
            }
        </style>
    </head>
    <body>
        <div id="fixed_top">
            <div class="d-flex justify-content-between">
                <div>
                                        <div class="d-inline-block header_qr"><img src='../temp/18102023073148.png' /></div>
                    <div class="d-inline-block header_title font-weight-bold mr-3">
                        Заказ №197-55<br />
                        от 06.09.2023                    </div>
                    <div class="d-inline-block header_title font-weight-bold mr-2">
                        Карта составлена:
                        <br />
                        Менеджер:
                    </div>
                    <div class="d-inline-block header_title">
                        06.09.2023 12:19                        <br />
                        Юлия Корнилова                    </div>
                </div>
                <div>
                    <div class="d-inline-block right_logo"><img src="../images/logo_with_label.svg" /></div>
                </div>
            </div>
            <div id="title">Заказчик: ООО "Полигранд"</div>
            <div id="subtitle">Наименование: этикетки 30 х 40</div>
            <div class="row">
                <div class="col-6 topproperty">
                    <strong>Объем заказа:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;148 000 шт&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1 232 м                </div>
                <div class="col-6 topproperty">
                    <strong>Тип работы:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Самоклеящиеся мат-лы                </div>
            </div>
        </div>
        <div id="placeholder_top"></div>
        <div id="main">
            <div class="row">
                <div class="col-4 border-right">
                    <table class="w-100">
                        <tr>
                            <td colspan="2" class="table-header font-weight-bold">ИНФОРМАЦИЯ ДЛЯ ПЕЧАТИ</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Печать</td>
                        </tr>
                        <tr>
                            <td>Машина</td>
                            <td>
                                Atlas                            </td>
                        </tr>
                                                <tr>
                            <td>Поставщик мат-ла</td>
                            <td>Любой</td>
                        </tr>
                                                <tr>
                            <td>Марка мат-ла</td>
                            <td>с/ка бумага полуглянец акрил</td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td class="text-nowrap">114 мкм&nbsp;&ndash;&nbsp;135, г/м<sup>2</sup></td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td>190 мм</td>
                        </tr>
                        <tr>
                            <td>На приладку 1 тиража</td>
                            <td>50 м</td>
                        </tr>
                                                <tr>
                            <td>Всего тиражей</td>
                            <td>2</td>
                        </tr>
                                                                        <tr>
                            <td>Всего мат-ла</td>
                            <td>1 470 м</td>
                        </tr>
                        <tr>
                            <td>Печать</td>
                            <td>
                                Лицевая                            </td>
                        </tr>
                        <tr>
                            <td>Рапорт</td>
                            <td>200,025</td>
                        </tr>
                        <tr>
                            <td>Растяг</td>
                            <td>Нет</td>
                        </tr>
                        <tr>
                            <td>Ширина этикетки</td>
                            <td>40 мм</td>
                        </tr>
                        <tr>
                            <td>Длина этикетки</td>
                            <td>30 мм</td>
                        </tr>
                        <tr>
                            <td>Кол-во ручьёв</td>
                            <td>4</td>
                        </tr>
                                                <tr>
                            <td>Этикеток в рапорте</td>
                            <td>6</td>
                        </tr>
                        <tr>
                            <td>Красочность</td>
                            <td>1 красок</td>
                        </tr>
                        <tr>
                            <td>Штамп</td>
                            <td>Старый</td>
                        </tr>
                                                <tr>
                            <td>Требование по материалу</td>
                            <td></td>
                        </tr>
                                            </table>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-6 border-right">
                            <table class="w-100">
                                <tr>
                                    <td colspan="2" class="table-header font-weight-bold"><br /> </td>
                                </tr>
                                                            </table>
                        </div>
                        <div class="col-6">
                            <table class="w-100">
                                <tr>
                                    <td colspan="2" class="table-header font-weight-bold">ИНФОРМАЦИЯ ДЛЯ РЕЗЧИКА</td>
                                </tr>
                                <tr>
                                    <td>Отгрузка в</td>
                                    <td>Шт</td>
                                </tr>
                                <tr>
                                    <td>Готовая продукция</td>
                                    <td>Записывать метраж</td>
                                </tr>
                                <tr>
                                    <td>Обр. шир. / Гор. зазор</td>
                                                                        <td>
                                        43,00 / 3,00 мм                                    </td>
                                </tr>
                                <tr>
                                    <td>Намотка до</td>
                                    <td>
                                        200 мм                                    </td>
                                </tr>
                                <tr>
                                    <td>Прим. метраж намотки</td>
                                    <td>
                                        Нет                                    </td>
                                </tr>
                                <tr>
                                    <td>Шпуля</td>
                                    <td>76 мм</td>
                                </tr>
                                <tr>
                                    <td>Этикеток в 1 м. пог.</td>
                                    <td>
                                        29,9963                                    </td>
                                </tr>
                                <tr>
                                    <td>Бирки</td>
                                    <td>
                                        Безликие                                    </td>
                                </tr>
                                <tr>
                                    <td>Склейки</td>
                                    <td>Помечать</td>
                                </tr>
                                <tr>
                                    <td>Отходы</td>
                                    <td>В кагат</td>
                                </tr>
                                <tr>
                                    <td>Упаковка</td>
                                    <td>
                                        Коробки                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="photolable">
                <span class="font-weight-bold">Фотометка:</span>&nbsp;
                Без фотометки            </div>
                        <table class="fotometka">
                <tr>
                    <td class="fotometka">
                        <img src="../images/roll/roll_type_1.png?1697607108" />
                                            </td>
                    <td class="fotometka fotochecked">
                        <img src="../images/roll/roll_type_2.png?1697607108" />
                        <br /><img src="../images/icons/check_black.svg" />                    </td>
                    <td class="fotometka">
                        <img src="../images/roll/roll_type_3.png?1697607108" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll/roll_type_4.png?1697607108" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll/roll_type_5.png?1697607108" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll/roll_type_6.png?1697607108" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll/roll_type_7.png?1697607108" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll/roll_type_8.png?1697607108" />
                                            </td>
                </tr>
            </table>
                </div>
            </div>
            
            <div class="font-weight-bold" style="font-size: 18px; margin-top: 10px;">Комментарий:</div>
            <div style="white-space: pre-wrap; font-size: 24px;"></div>
                        <div class="break_page"></div>
            <div class="row">
                                <div class="col-3">
                    <div class="mt-4 mb-2 printing_title font-weight-bold">Тираж 1</div>
                    <div class="d-flex justify-content-between font-italic border-bottom">
                        <div>50 000 шт</div>
                        <div>417 м</div>
                    </div>
                    <table class="mb-3 w-100">
                                            <tr>
                            <td>
                                Kontur                            </td>
                            <td id="cliche_3854_1">
                                Новая Flint 1.7                            </td>
                        </tr>
                                        </table>
                </div>
                                <div class="col-3">
                    <div class="mt-4 mb-2 printing_title font-weight-bold">Тираж 2</div>
                    <div class="d-flex justify-content-between font-italic border-bottom">
                        <div>98 000 шт</div>
                        <div>817 м</div>
                    </div>
                    <table class="mb-3 w-100">
                                            <tr>
                            <td>
                                Kontur                            </td>
                            <td id="cliche_3855_1">
                                Новая Flint 1.7                            </td>
                        </tr>
                                        </table>
                </div>
                            </div>
                        <div id="fixed_bottom">
                <table class="w-100">
                    <tr class="left">
                        <td>Дизайнер:</td>
                        <td>Менеджер:</td>
                    </tr>
                </table>
            </div>
                    </div>
        <script>
            var css = '@page { size: portrait; margin: 8mm; }',
                    head = document.head || document.getElementsByTagName('head')[0],
                    style = document.createElement('style');
            
            style.type = 'text/css';
            style.media = 'print';
            
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            
            head.appendChild(style);
            
            window.print();
        </script>
    </body>
</html>