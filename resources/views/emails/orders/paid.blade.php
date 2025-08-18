<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Urbanist:wght@400;600&display=swap');
  </style>
</head>
<body style="font-family: 'Urbanist', 'Trebuchet MS', sans-serif; background-color: #f7f7f7; padding: 20px; color: #333333;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px;">
    <tr>
      <td align="center" style="padding-bottom: 20px; background-color: #ffffff; height: 80px;">
        <img src="{{ asset('images/koderspedia.png') }}"
             alt="Koderspedia Logo"
             width="150"
             style="display: block; font-family: 'Trebuchet MS', sans-serif; font-size: 18px; color: #333333; background-color: #ffffff; text-align: center;">
      </td>
    </tr>
    <tr>
      <td style="font-size: 18px; font-weight: 600; padding-bottom: 10px;">Hi {{ $order->client->name }},</td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-bottom: 20px;">
        We’re pleased to confirm that Order #ODR-{{ $order->id }} has been successfully paid. Below are the details of your transaction:
      </td>
    </tr>
    <tr>
      <td>
        <table width="100%" cellpadding="10" cellspacing="0" border="1" style="border-collapse: collapse; font-size: 15px;">
          <tr><th align="left">Field</th><th align="left">Details</th></tr>
          <tr><td>Customer</td><td>{{ $order->client->name }}</td></tr>
          <tr><td>Created By</td><td>{{ $order->createdBy->name }}</td></tr>
          <tr><td>Client Of</td><td>{{ ucwords($order->brand->name) }}</td></tr>
          <tr><td>Invoice Amount</td><td>${{ $order->price }}</td></tr>
          <tr><td>Tipped Amount</td><td>${{ $order->tip }}</td></tr>
          <tr><td>Total</td><td>${{ $order->price + $order->tip }}</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-top: 20px;">
        You can view the full invoice and order details in your dashboard:<br><br>
        <a href="https://koderspedia-dashboard.koderspedia.net" style="color: #1a73e8;">View in Dashboard</a>
      </td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-top: 20px;">
        If you have any questions or need further assistance, feel free to reach out at any time.
      </td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-top: 20px;">
        Thanks again for choosing Koderspedia!<br><br>
        <strong>Team Koderspedia</strong><br>
        <a href="mailto:sales@koderspedia.com">sales@koderspedia.com</a><br>
        <a href="https://www.koderspedia.com">www.koderspedia.com</a>
      </td>
    </tr>
  </table>
</body>
</html>
