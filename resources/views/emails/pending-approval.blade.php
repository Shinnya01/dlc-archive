<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Pending Approval</title>
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

    /* Dark mode adjustments */
    @media (prefers-color-scheme: dark) {
      body {
        background-color: #1e1e1e !important;
        color: #e0e0e0 !important;
      }

      table {
        background-color: #2b2b2b !important;
      }
    }
  </style>
</head>
<body>
  <table cellpadding="0" cellspacing="0" role="presentation">
    <tr>
      <td align="center">
        <h2 style="color: #800000">Hello {{ $userName }},</h2>

        <p>Your request is currently <strong style="color: #ffcc00;">waiting for admin approval</strong>.</p>

        <p>You’ll receive another email once it has been reviewed.</p>

        <p>If you have any questions, please contact <a href="https://www.facebook.com/DONTUOCHMYBIRDIE">Lascano Leeann</a>.</p>
        </p>

        <p>Best regards,<br>PLC - Archive Team</p>
      </td>
    </tr>
  </table>
</body>
</html>
