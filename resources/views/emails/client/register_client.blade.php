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
      <td style="font-size: 18px; font-weight: 600; padding-bottom: 10px;">Hi {{ $client->name }},</td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-bottom: 20px;">
        Thank you for registering with Koderspedia! Your account has been successfully created. You can now log in to your dashboard to view invoices, manage your projects, and explore other services we offer.
      </td>
    </tr>
    <tr>
      <td>
        <table width="100%" cellpadding="10" cellspacing="0" border="1" style="border-collapse: collapse; font-size: 15px;">
          <tr><th align="left">Field</th><th align="left">Details</th></tr>
          <tr><td>Name</td><td>{{ $client->name }}</td></tr>
          <tr><td>Email</td><td>{{ $client->email }}</td></tr>
          <tr><td>Username</td><td>{{ $client->username }}</td></tr>
          <tr><td>Password</td><td>{{ $client->plainPassword }}</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="font-size: 14px; padding-top: 20px; color: #b00000;">
        Please keep this information secure and do not share it with anyone.
      </td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-top: 20px;">
        You can log in to your account anytime here:<br><br>
        <a href="https://koderspedia-dashboard.koderspedia.net" style="color: #1a73e8;">Login to Dashboard</a>
      </td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-top: 20px;">
        If you have any questions or need assistance, feel free to reply to this email or reach out to our support team.
      </td>
    </tr>
    <tr>
      <td style="font-size: 16px; padding-top: 20px;">
        Thanks,<br><br>
        <strong>Team Koderspedia</strong><br>
        <a href="mailto:sales@koderspedia.com">sales@koderspedia.com</a><br>
        <a href="https://www.koderspedia.com">www.koderspedia.com</a>
      </td>
    </tr>
  </table>
</body>
</html>

