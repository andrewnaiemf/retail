<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: right;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: left;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="path_to_logo" style="width:100%; max-width:300px;">
                        </td>
                        <td>
                            <strong>مؤسسة العناية المتوجة للسيارات</strong><br>
                            المركز الرئيسي<br>
                            +966550770201
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <strong>مستلم من:</strong><br>
                            مؤسسة العناية المتوجة للسيارات<br>
                            المرجع: PYT1866<br>
                            التاريخ: 2024-05-16<br>
                            النوع: قبض<br>
                            الحساب: BANK OF AL RAJHI 475000010006086449858
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td>المرجع #</td>
            <td>المبلغ</td>
            <td>المبلغ المخصص</td>
            <td>التاريخ</td>
        </tr>
        <tr class="item">
            <td>DR-IN2051</td>
            <td>10120.00 ر.س</td>
            <td>10120.00 ر.س</td>
            <td>2024-05-16</td>
        </tr>
        <tr class="item">
            <td>DR-IN2083</td>
            <td>115000.00 ر.س</td>
            <td>115000.00 ر.س</td>
            <td>2024-05-16</td>
        </tr>
        <tr class="item">
            <td>DR-IN2098</td>
            <td>92000.00 ر.س</td>
            <td>92000.00 ر.س</td>
            <td>2024-05-16</td>
        </tr>
        <tr class="item last">
            <td>DR-IN2116</td>
            <td>8280.00 ر.س</td>
            <td>8280.00 ر.س</td>
            <td>2024-05-16</td>
        </tr>
        <tr class="total">
            <td colspan="2"></td>
            <td>225400.00 ر.س</td>
            <td>المبلغ المخصص</td>
        </tr>
    </table>
</div>
</body>
</html>
