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
            padding: 20px;
            border: 2px solid #000;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 16px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: right;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 3px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 10px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 20px;
            line-height: 15px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 30px;
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
        .total {
            border-collapse: collapse;
            width: 100%;
            background-color: white;
            margin-bottom: 20px;
        }
        .total td {
            border: 1px solid #dee2e6;
            padding: 15px;
            text-align: right;
            font-size: 16px;
        }
        .total td:first-child {
            width: 40%;
            padding-right: 10px;
        }
        .total td:last-child {
            width: 60%;
            background-color: #f1f1f1;
        }
        @media print {
            .invoice-box {
                box-shadow: none;
                margin: 0;
                padding: 0;
                border: none;
            }
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
            }
            .total {
                page-break-inside: avoid;
            }
            .invoice-box table {
                border-collapse: collapse;
                width: 100%;
            }
            .invoice-box table tr td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="4" style="border-bottom: 1px solid #ccc">
                <table style="width: 100%;">
                    <tr>
                        <td class="title" style="width: 20%;">
                            <img src="{{'logo.jpg'}}" style="width:100px; max-width:100px;">
                        </td>

                        <td style="width: 60%;">
                            <strong>سند قبض</strong><br>
                            <strong>مؤسسة العناية المتوجة للسيارات</strong><br>
                            المركز الرئيسي<br>
                            +966550770201
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="information">
            <td colspan="4">
                <table style="width: 100%;">
                    <tr>
                        <td>
                            <div>
                                <span>{{$data['customer']['name']}}</span>
                                <strong>مستلم من:</strong>
                            </div>
                            <div>
                                <span>{{$data['receipt']['reference']}}</span>
                                <strong> المرجع:</strong>
                            </div>
                            <div>
                                <span>{{$data['receipt']['date']}}</span>
                                <strong> التاريخ:</strong>
                            </div>
                            <div>
                                <span>قبض</span>
                                <strong> النوع:</strong>
                            </div>
                            <div>
                                <span>{{$data['account']['name_ar']}}</span>
                                <strong> الحساب:</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">
                <table class="total">
                    <tr>
                        <td dir="rtl"> <span>رس</span> {{$data['receipt']['un_allocate_amount']}}   </td>
                        <td>المبلغ غير المخصص</td>
                    </tr>
                    <tr>
                        <td dir="rtl"> <span>رس</span>  <span>{{$data['receipt']['allocated_amount']}} </span>  </td>
                        <td>المبلغ المخصص</td>
                    </tr>
                    <tr>
                        <td dir="rtl"> <span>رس</span> <span>{{$data['receipt']['amount']}}</span> </td>
                        <td>المبلغ</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td style="width: 25%;">التاريخ</td>
            <td style="width: 25%;">المبلغ المستحق</td>
            <td style="width: 25%;">المبلغ المخصص</td>
            <td style="width: 25%;">المبلغ</td>
            <td style="width: 25%;"> المرجع</td>
        </tr>
        @foreach($data['receipt']['allocates'] as $allocate)
            @php
                $invoice = \App\Models\Invoice::find($allocate['allocatee'][0]['id']);
            @endphp
            <tr class="item">
                <td>{{$allocate['date']}}</td>
                <td dir="rtl"><span>{{'ر.س'}}</span>  <span>{{$invoice['due_amount']}}</span></td>

                <td dir="rtl"><span>{{'ر.س'}}</span>  <span>{{$allocate['amount']}}</span></td>
                <td dir="rtl"> <span>{{'ر.س'}}</span>  <span>{{$allocate['allocatee'][0]['total']}}</span></td>
                <td>{{$allocate['allocatee'][0]['reference']}}</td>
            </tr>
        @endforeach
    </table>
</div>
</body>
</html>
