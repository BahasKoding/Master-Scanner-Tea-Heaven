<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Quotation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #007bff;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
        }
        h1 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .quotation-number {
            font-size: 18px;
            font-weight: bold;
            color: #6c757d;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $message->embed(public_path('img/logo-iaf.jpeg')) }}" alt="Company Logo" style="max-width: 200px;">
    </div>
    <div class="content">
        <h1>Sales Quotation</h1>
        <p class="quotation-number">Quotation Number: {{ $salesQuotation->quotation_number }}</p>
        <p>Dear {{ $salesQuotation->customer->customer }},</p>
        <p>Thank you for your interest in our products/services. Please find attached our sales quotation for your review.</p>
        <p class="total-amount">Total Amount: {{ number_format($salesQuotation->total_amount, 2, ',', '.') }}</p>
        <p>If you have any questions or need further clarification, please don't hesitate to contact us. We're here to assist you.</p>
        <p>We appreciate your consideration and look forward to the possibility of doing business with you.</p>
        <p>Best regards,<br>Your Sales Team</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>