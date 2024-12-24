<?php include_once "DBData.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once 'PHPMailer/Exception.php';
include_once 'PHPMailer/PHPMailer.php';
include_once 'PHPMailer/SMTP.php';

$existeError = false;
$emailSent = false;

if (isset($_POST["sendDaySummaryEmail"]) && isset($_POST["sendDaySummaryDesign"]) && connection_status() == 0) {
	//Get backup info
	$backupName = "";
	$backupInfo = "";

	$directoryBackups = "backups/";
	$scanBackups = scandir($directoryBackups);
	if ($scanBackups[2]) {
		for ($x = 2; $x < count($scanBackups); ++$x) {
			$backupConvertToDate = substr(substr($scanBackups[$x], 0, -4), 7, 10);

			if ($backupConvertToDate == date('Y-m-d')) {
				$backupName = $scanBackups[$x];
				$backupInfo = '<h3 style="text-align: center;">Backup Information</h3><p>Attached to this email you will find a file named <b>' . $scanBackups[$x] . '</b>. This is a complete backup of your POS System which you can use if needed.</p><hr>';
			}
		}
	}

	//Send mail
	$mail = new PHPMailer(true);
	try {
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = phpmailer_host;
		$mail->SMTPAuth = true;
		$mail->Username = phpmailer_username;
		$mail->Password = phpmailer_password;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = 465;

		//Recipients
		$mail->setFrom(phpmailer_username, 'POS System by TRV Solutions');
		$mail->addAddress($_POST["sendDaySummaryEmail"]);

		// Content
		$mail->isHTML(true);
		if ($backupName != "") {
			$mail->addAttachment('backups/' . $backupName);
		}
		$mail->Subject = 'Day Summary';
		$mail->Body = '<html>
	<head>
	<meta charset= "UTF-8">
	<meta name= "viewport" content= "width=device-width, initial-scale=1">
	<title>Day Summary</title>
	</head>
	<body style= "background-color: #e6e7e8;">
	<div style= "max-width: 640px;background-color: #fff;margin: auto;padding: 10px;">	
	<div>' . $_POST["sendDaySummaryDesign"] . '</div>
	<hr>
	' . $backupInfo . '
	<p style= "text-align: center;">This email has been automatically generated by <a href= "https://www.trvsolutions.com" target= "_blank">TRV Solutions</a>.</p>
	</div>
	</body>
	</html>';

		$mail->send();
		$emailSent = true;
	} catch (Exception $e) {
		$emailSent = false;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'email_enviado' => $emailSent
);
echo json_encode(convertJson($varsSend));
?>