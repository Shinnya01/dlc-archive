<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Account Approved</title>
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <style>
    body {
      margin: 0;
      padding: 40px 0;
      background-color: #f2f2f2;
      color: #000;
    }

    table {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 8px;
      padding: 30px;
    }

    a.button {
      display: inline-block;
      background: #800000;
      color: #ffffff !important;
      padding: 10px 24px;
      text-decoration: none;
      border-radius: 6px;
    }

    /* Dark mode adjustments */
    @media (prefers-color-scheme: dark) {
      body {
        background-color: #1e1e1e !important;
        color: #e0e0e0 !important;
      }

      table {
        background-color: #2b2b2b !important;
      }

      a.button {
        background-color: #a83232 !important;
      }
    }
  </style>
</head>
<body>
  <table cellpadding="0" cellspacing="0" role="presentation">
    <tr>
      <td align="center">
        <h2>Hello {{ $userName }},</h2>

        <p>Your account request has been <strong>approved by the admin</strong>.</p>

        <p>You can now log in and access your account.</p>

        <p style="margin:30px 0;">
          <a href="{{ url('/') }}" class="button">Login</a>
        </p>

        <p>Best regards,<br>{{ config('app.name') }} Team</p>
      </td>
    </tr>
  </table>
</body>
</html>
