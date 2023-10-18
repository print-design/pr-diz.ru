
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
                                        <div class="d-inline-block header_qr"><img src='../temp/18102023073802.png' /></div>
                    <div class="d-inline-block header_title font-weight-bold mr-3">
                        Заказ №36-96<br />
                        от 12.09.2023                    </div>
                    <div class="d-inline-block header_title font-weight-bold mr-2">
                        Карта составлена:
                        <br />
                        Менеджер:
                    </div>
                    <div class="d-inline-block header_title">
                        12.09.2023 13:02                        <br />
                        Ольга Снесарева                    </div>
                </div>
                <div>
                    <div class="d-inline-block right_logo"><img src="../images/logo_with_label.svg" /></div>
                </div>
            </div>
            <div id="title">Заказчик: ООО Флора МК</div>
            <div id="subtitle">Наименование: Мыло банное 180 гр</div>
            <div class="row">
                <div class="col-6 topproperty">
                    <strong>Объем заказа:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;300 кг&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20 053 м                </div>
                <div class="col-6 topproperty">
                    <strong>Тип работы:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Пленка с печатью                </div>
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
                                ZBS                            </td>
                        </tr>
                                                <tr>
                            <td>Марка мат-ла</td>
                            <td>HGPL прозрачка</td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td class="text-nowrap">20 мкм&nbsp;&ndash;&nbsp;18,2 г/м<sup>2</sup></td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td>420 мм</td>
                        </tr>
                        <tr>
                            <td>Метраж на приладку</td>
                            <td>1 000 м</td>
                        </tr>
                                                                        <tr>
                            <td>Метраж на тираж</td>
                            <td>20 053 м</td>
                        </tr>
                                                <tr>
                            <td>Всего мат-ла</td>
                            <td>21 855 м</td>
                        </tr>
                        <tr>
                            <td>Печать</td>
                            <td>
                                Оборотная                            </td>
                        </tr>
                        <tr>
                            <td>Рапорт</td>
                            <td>301,625</td>
                        </tr>
                        <tr>
                            <td>Растяг</td>
                            <td>Нет</td>
                        </tr>
                        <tr>
                            <td>Ширина ручья</td>
                            <td>200 мм</td>
                        </tr>
                        <tr>
                            <td>Длина этикетки</td>
                            <td>151 мм</td>
                        </tr>
                        <tr>
                            <td>Кол-во ручьёв</td>
                            <td>2</td>
                        </tr>
                                                <tr>
                            <td>Требование по материалу</td>
                            <td>П5384 база</td>
                        </tr>
                                                <tr>
                            <td colspan="2" class="font-weight-bold border-bottom-2">Красочность: 5 красок</td>
                        </tr>
                                                <tr>
                            <td>
                                Cyan                            </td>
                            <td>
                                Старая                            </td>
                        </tr>
                                                <tr>
                            <td>
                                Magenda                            </td>
                            <td>
                                Старая                            </td>
                        </tr>
                                                <tr>
                            <td>
                                Yellow                            </td>
                            <td>
                                Старая                            </td>
                        </tr>
                                                <tr>
                            <td>
                                Kontur                            </td>
                            <td>
                                Старая                            </td>
                        </tr>
                                                <tr>
                            <td>
                                P1                            </td>
                            <td>
                                Старая                            </td>
                        </tr>
                                                                    </table>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-6 border-right">
                            <table class="w-100">
                                <tr>
                                    <td colspan="2" class="table-header font-weight-bold"> ИНФОРМАЦИЯ ДЛЯ ЛАМИНАЦИИ</td>
                                </tr>
                                                                <tr>
                                    <td>Кол-во ламинаций</td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-weight-bold">Ламинация 1</td>
                                </tr>
                                <tr>
                                    <td>Марка пленки</td>
                                    <td>HWHL белая</td>
                                </tr>
                                <tr>
                                    <td>Толщина</td>
                                    <td class="text-nowrap">20 мкм&nbsp;&ndash;&nbsp;19,2 г/м<sup>2</sup></td>
                                </tr>
                                <tr>
                                    <td>Ширина мат-ла</td>
                                    <td>420 мм</td>
                                </tr>
                                <tr>
                                    <td>Метраж на приладку</td>
                                    <td>200 м</td>
                                </tr>
                                <tr>
                                    <td>Метраж на тираж</td>
                                    <td>20 053 м</td>
                                </tr>
                                <tr>
                                    <td>Всего мат-ла</td>
                                    <td>20 855 м</td>
                                </tr>
                                <tr>
                                    <td>Ламинационный вал</td>
                                    <td>410 мм</td>
                                </tr>
                                <tr>
                                    <td>Анилокс</td>
                                    <td>Нет</td>
                                </tr>
                                <tr>
                                    <td>Требование по материалу</td>
                                    <td>П5083 В3</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-weight-bold border-bottom-2">Ламинация 2</td>
                                </tr>
                                <tr>
                                    <td>Марка пленки</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Толщина</td>
                                    <td class="text-nowrap">0 мкм&nbsp;&ndash;&nbsp;0, г/м<sup>2</sup></td>
                                </tr>
                                <tr>
                                    <td>Ширина мат-ла</td>
                                    <td>0 мм</td>
                                </tr>
                                <tr>
                                    <td>Метраж на приладку</td>
                                    <td>0 м</td>
                                </tr>
                                <tr>
                                    <td>Метраж на тираж</td>
                                    <td>0 м</td>
                                </tr>
                                <tr>
                                    <td>Всего мат-ла</td>
                                    <td>0 м</td>
                                </tr>
                                <tr>
                                    <td>Требование по материалу</td>
                                    <td></td>
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
                                    <td>Кг</td>
                                </tr>
                                <tr>
                                    <td>Готовая продукция</td>
                                    <td>Взвешивать</td>
                                </tr>
                                <tr>
                                    <td>Обрезная ширина</td>
                                                                        <td>
                                        200 мм                                    </td>
                                </tr>
                                <tr>
                                    <td>Намотка до</td>
                                    <td>
                                        300 мм                                    </td>
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
                                        6,6313                                    </td>
                                </tr>
                                <tr>
                                    <td>Бирки</td>
                                    <td>
                                        Принт-Дизайн                                    </td>
                                </tr>
                                <tr>
                                    <td>Склейки</td>
                                    <td>Помечать</td>
                                </tr>
                                <tr>
                                    <td>Отходы</td>
                                    <td>В пресс</td>
                                </tr>
                                <tr>
                                    <td>Упаковка</td>
                                    <td>
                                        Паллетирование                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="photolable">
                <span class="font-weight-bold">Фотометка:</span>&nbsp;
                Левая            </div>
                        <table class="fotometka">
                <tr>
                    <td class="fotometka">
                        <img src="../images/roll_left/roll_type_1.png?1697607482" />
                                            </td>
                    <td class="fotometka fotochecked">
                        <img src="../images/roll_left/roll_type_2.png?1697607482" />
                        <br /><img src="../images/icons/check_black.svg" />                    </td>
                    <td class="fotometka">
                        <img src="../images/roll_left/roll_type_3.png?1697607482" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll_left/roll_type_4.png?1697607482" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll_left/roll_type_5.png?1697607482" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll_left/roll_type_6.png?1697607482" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll_left/roll_type_7.png?1697607482" />
                                            </td>
                    <td class="fotometka">
                        <img src="../images/roll_left/roll_type_8.png?1697607482" />
                                            </td>
                </tr>
            </table>
                </div>
            </div>
            
            <div class="font-weight-bold" style="font-size: 18px; margin-top: 10px;">Комментарий:</div>
            <div style="white-space: pre-wrap; font-size: 24px;">старые</div>
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