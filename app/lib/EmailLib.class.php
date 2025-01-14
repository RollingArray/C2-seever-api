<?php

namespace C2\Email;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailLib
{

	protected $settings = null;
	protected $transport;
	protected $mailer;

	//__construct
	function __construct($settings)
	{
		$this->settings = $settings;

		$smtp_host_ip = gethostbyname($this->settings['email']['smtpHostIp']);

		$this->transport = (new \Swift_SmtpTransport($smtp_host_ip, $this->settings['email']['port']))
			->setUsername($this->settings['email']['smtpUsername'])
			->setPassword($this->settings['email']['smtpPassword']);

		$this->mailer = new \Swift_Mailer($this->transport);;
	}

	//__destruct
	function __destruct()
	{
		//
	}

	//generateemail_track_id
	public function generateemail_track_id()
	{
		return uniqid('TRACKID_');
	}


	private function sendEmail($DBAccessLib, $subject, $passedEmailData, $passedData)
	{
		$insertNewEmailTrack = $DBAccessLib->insertNewEmailTrack($passedEmailData);
		if ($insertNewEmailTrack) {
			$email = (new \Swift_Message($subject))
				->setFrom(array($this->settings['email']['supportEmail'] => $this->settings['email']['prettyEmailName']))
				->setTo(array($passedEmailData['user_email']))
				->setBody($this->messageFormat($passedEmailData), 'text/html');

			$this->mailer->send($email);
		}
	}

	//message format
	function messageFormat($passedData)
	{
		$date = date("l jS \of F Y");
		$htmlTemplate = '
			<img src="' . $this->settings['api']['host'] . '/' . $this->settings['email']['emailTrack'] . '/' . $passedData['email_track_id'] . '" width="1" height="1" />
			<div class="">
			<div class="aHl"></div>
			<div id=":19g" tabindex="-1"></div>
			<div id=":195" class="ii gt">
			<div id=":194" class="a3s aXjCH ">
				<div
				style="font-family:roboto,sans-serif;border:1px solid #e0e0e0;background-color:white;max-width:600px;margin:0 auto">
				<div style="background-color:#323433;padding:24px 0"><img
					style="margin:auto;display:block;"
					src="https://c2.api.rollingarray.co.in/img/app_email_header.png"
					class="CToWUd"></div>
				<table style="width:100%;background-color:#ffa000" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<td style="padding:24px">
						<div style="font-size:20px;line-height:24px;color:#323433">' . $passedData['email_header'] . '</div>
						</td>
					</tr>
					<tr></tr>
					</tbody>
				</table>
				<div style="margin-bottom:24px;padding:24px 24px 0 24px">
					<table style="font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0;padding:0" cellpadding="0"
					cellspacing="0" width="100%">
					<tbody>
						<tr style="font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0;padding:0">
						<td
							style="font-family:Helvetica,Arial,sans-serif;font-size:12px;vertical-align:top;margin:0;padding:0 0 0px;float:right"
							valign="top">
							' . $date . '
						</td>
						</tr>
						<tr style="font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0;padding:0">
						<td
							style="font-family:Helvetica,Arial,sans-serif;font-size:14px;vertical-align:top;margin:0;padding:0 0 20px"
							valign="top">
							Dear <strong
							style="font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0;padding:0">' . $passedData['user_full_name'] . '
							</strong>,
						</td>
						</tr>
						<tr style="font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0;padding:0">
						<td
							style="font-family:Helvetica,Arial,sans-serif;font-size:14px;vertical-align:top;margin:0;padding:0 0 20px"
							valign="top">
							' . $passedData['email_content'] . '
							<br><br>
						</td>
						</tr>
						<tr style="font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0;padding:0">
						<td
							style="font-family:Helvetica,Arial,sans-serif;font-size:14px;vertical-align:top;margin:0;padding:0 0 20px"
							valign="top"><b>
							Regards<br>
							Team ' . $this->settings['email']['appName'] . '</b>
						</td>
						</tr>
						<tr style="font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0;padding:0">
						<td
							style="font-family:Helvetica,Arial,sans-serif;font-size:14px;vertical-align:top;margin:0;padding:0 0 20px;font-style:italic"
							valign="top">
							' . $this->settings['email']['appName'] . ' - ' . $this->settings['email']['appTagLine'] . '
						</td>
						</tr>
					</tbody>
					</table>
				</div>
				<div style="background-color:#d7d8da;padding:20px">
					<table style="width:100%" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
						<td>
							<div style="font-family:Helvetica,Arial,sans-serif; font-size:20px;line-height:24px; text-align: center; padding:20px">
							Connect from your favorite space
							</div>
						</td>
						</tr>
						<tr>
						<td>
							<a href="https://c2.rollingarray.co.in" target="_blank">
							<img style="height:60px; display: block; margin-left: auto; margin-right: auto;"
							src="https://c2.api.rollingarray.co.in/img/devices.png"
							class="CToWUd">
							</a>
						</td>
						</tr>
					</tbody>
					</table>
				</div>
				<div style="background-color:#e0e0e0;height:1px;width:100%"></div>
				<div style="background-color:#e0e0e0;height:1px;width:100%"></div>
				<div style="background-color:#eceff1;padding:24px;font-size:12px;line-height:16px">
					<div>You are receiving this notification because you have registered with <span style="color:#56C2E1"> <a
					style="text-decoration:none;color:#039be5" href="https://c2.rollingarray.co.in/"
					target="_blank">' . $this->settings['email']['appName'] . '</a></span></div>
					<div style="margin-top:24px">Thanks for using ' . $this->settings['email']['appName'] . ' !</div>
				</div>
				<div style="background-color: #323433; padding: 20px">
                    <table
                        style="width: 100%; text-align: center; color: #fff"
                        cellpadding="0"
                        cellspacing="0"
                    >
                        <tbody>
                            <tr>
                                <td>
                                    <a
                                        style="
                                            font-size: 12px;
                                            text-decoration: underline;
                                            color: #fff;
                                        "
                                        href="https://c2.rollingarray.co.in/"
                                        target="_blank"
                                    >
                                        C2
                                    </a>
                                    &nbsp; | &nbsp;
                                    <a
                                        style="
                                            font-size: 12px;
                                            text-decoration: underline;
                                            color: #fff;
                                        "
                                        href="https://github.com/RollingArray/C2-storyline"
                                        target="_blank"
                                    >
                                        Github
                                    </a>
                                    &nbsp; | &nbsp;
									<a
                                        style="
                                            font-size: 12px;
                                            text-decoration: underline;
                                            color: #fff;
                                        "
                                        href="https://c2.doc.rollingarray.co.in/"
                                        target="_blank"
                                    >
                                        Help & Doc
                                    </a>
									&nbsp; | &nbsp;
                                    <a
                                        style="
                                            font-size: 12px;
                                            text-decoration: underline;
                                            color: #fff;
                                        "
                                        href="https://c2.doc.rollingarray.co.in/#/go/articles/terms-conditions"
                                        target="_blank"
                                    >
                                        Terms
                                    </a>
                                    &nbsp; | &nbsp;
                                    <a
                                        style="
                                            font-size: 12px;
                                            text-decoration: underline;
                                            color: #fff;
                                        "
                                        href="https://c2.doc.rollingarray.co.in/#/go/articles/privacy-policy"
                                        target="_blank"
                                    >
                                        Privacy
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				<div style="background-color:#323433;padding:34px">
					<table style="width:100%" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
						<td>
							<a href="https://rollingarray.co.in/" target="_blank">
							<img style="height:34px;max-height:34px;min-height:34px"
							src="https://rollingarray.co.in/images/ra_brand_icon_email.png"
							class="CToWUd">
							</a>
							</td>
						<td>
							<div style="font-size:10px;line-height:14px;font-weight:400;text-align:right"><a
								style="color:#d6dde1;text-decoration:none">&copy; ' . date("Y") . ' rollingarray.co.in<br>Bangalore, India. </a></div>
						</td>
						</tr>
					</tbody>
					</table>
				</div>
				</div>
			</div>
			<div class="yj6qo"></div>
			</div>
			<div id=":19l" class="ii gt" style="display:none">
			<div id=":19k" class="a3s aXjCH undefined"></div>
			</div>
			<div class="hi"></div>
		</div>
  		';
		return $htmlTemplate;
	}

	//send test email
	function sendTestEmail($DBAccessLib, $UtilityLib, $passedData)
	{
		$email_track_id = $UtilityLib->generateId('EMAILTRACK_');
		$header = 'Test email';
		$subject = 'Test email subject';
		$emailBodyMessage = '
      Test email
    ';

		$passedEmailData = array(
			"user_full_name" => $passedData['user_full_name'],
			"email_track_id" => $email_track_id,
			"user_email" => $passedData['user_email'],
			"email_subject" => $subject,
			"email_content" => $emailBodyMessage,
			"email_header" => $header,
		);

		$this->sendEmail($DBAccessLib, $subject, $passedEmailData, $passedData);
	}

	//send sign up verification code
	function sendSignUpVerificationCode($DBAccessLib, $UtilityLib, $passedData)
	{
		if ($passedData['user_full_name'] && $passedData['user_verification_code'] && $passedData['user_email']) {
			$email_track_id = $UtilityLib->generateId('EMAILTRACK_');
			$header = 'You are just a step away from using your account in ' . $this->settings['email']['appName'];
			$subject = $this->settings['email']['appName'] . ' - Email Verification';
			$emailBodyMessage = '
        You or someone with your email id signed up at ' . $this->settings['email']['appName'] . '. Your account is almost ready, but before you login you need to confirm your email by applying below verification code in the app.
        <br><br>
		<table border=0 style="font-family:Helvetica,Arial,sans-serif;font-size:5em;vertical-align:top;margin:0;margin:0 0 20px; background: #e9e8e8;
		border: 1px solid #d1cdcd;
		border-radius: 10px; width: 100%; text-align: center;width: 100%;font-size: 5em;">
              <tr>
                  <td>' . $passedData['user_verification_code'] . '</td>
              </tr>
              
        </table>
        
        If you haven’t requested this email, there’s nothing to worry about – you can safely ignore it.
      ';

			$passedEmailData = array(
				"user_full_name" => $passedData['user_full_name'],
				"email_track_id" => $email_track_id,
				"user_email" => $passedData['user_email'],
				"email_subject" => $subject,
				"email_content" => $emailBodyMessage,
				"email_header" => $header,
			);

			$this->sendEmail($DBAccessLib, $subject, $passedEmailData, $passedData);
		}
	}

	//mass inactive send sign up verification code
	function massInactiveSendSignUpVerificationCode($DBAccessLib, $passedData)
	{
		//return 'asd';
		if ($passedData['user_full_name'] && $passedData['user_verification_code'] && $passedData['user_email'] && $passedData['email_track_id']) {
			$header = 'You are just a step away from using your account in ' . $this->settings['email']['appName'];
			$subject = $this->settings['email']['appName'] . ' - Email Verification';
			$emailBodyMessage = '
        You or someone with your email id signed up at ' . $this->settings['email']['appName'] . '. Your account is almost ready, but before you login you need to confirm your email by applying below verification code in the app.
        <br><br>
        <table border=0 style="font-family:Helvetica,Arial,sans-serif;font-size:5em;vertical-align:top;margin:0;margin:0 0 20px; background: #e9e8e8;
		border: 1px solid #d1cdcd;
		border-radius: 10px; width: 100%; text-align: center;width: 100%;font-size: 5em;">
              <tr>
                  <td>' . $passedData['user_verification_code'] . '</td>
              </tr>
              
        </table>
        
        If you haven’t requested this email, there’s nothing to worry about – you can safely ignore it.
      ';

			$passedEmailData = array(
				"user_full_name" => $passedData['user_full_name'],
				"email_track_id" => $passedData['email_track_id'],
				"user_email" => $passedData['user_email'],
				"email_subject" => $subject,
				"email_content" => $emailBodyMessage,
				"email_header" => $header,
			);

			//echo json_encode($passedEmailData);

			$this->sendEmail($DBAccessLib, $subject, $passedEmailData, $passedData);
		}
	}

	//send sign up verification code
	function signUpSuccess($DBAccessLib, $UtilityLib, $passedData)
	{
		if ($passedData['user_full_name'] && $passedData['user_email']) {
			$email_track_id = $UtilityLib->generateId('EMAILTRACK_');
			$header = 'Welcome to ' . $this->settings['email']['appName'];
			$subject = $this->settings['email']['appName'] . ' - Welcome';

			$emailBodyMessage = '
        Welcome to ' . $this->settings['email']['appName'] . ' :-) Your account is active and we are waiting for you to create your Project or join a ongoing Project
        <br><br>
      ';

			$passedEmailData = array(
				"user_full_name" => $passedData['user_full_name'],
				"email_track_id" => $email_track_id,
				"user_email" => $passedData['user_email'],
				"email_subject" => $subject,
				"email_content" => $emailBodyMessage,
				"email_header" => $header,
			);

			$this->sendEmail($DBAccessLib, $subject, $passedEmailData, $passedData);
		}
	}

	//send password reset code
	function sendPasswordResetCode($DBAccessLib, $UtilityLib, $passedData)
	{
		if ($passedData['user_full_name'] && $passedData['user_password_reset_code'] && $passedData['user_email']) {
			$email_track_id = $UtilityLib->generateId('EMAILTRACK_');
			$header = 'Did you forgot your password';
			$subject = $this->settings['email']['appName'] . ' - Reset Password';
			$emailBodyMessage = '
        You told us you forgot your password. If you really did, use this code to reset your password.
        <br><br>
        <table border=0 style="font-family:Helvetica,Arial,sans-serif;font-size:14px;vertical-align:top;margin:0;padding:0 0 20px">
              <tr>
                  <td><b>Reset Code</b></td>
                  <td>' . $passedData['user_password_reset_code'] . '</td>
              </tr>
              
        </table>
        If you did not mean to reset your password, then you can just ignore this email. Your password will not change.
      ';

			$passedEmailData = array(
				"user_full_name" => $passedData['user_full_name'],
				"email_track_id" => $email_track_id,
				"user_email" => $passedData['user_email'],
				"email_subject" => $subject,
				"email_content" => $emailBodyMessage,
				"email_header" => $header,
			);

			$this->sendEmail($DBAccessLib, $subject, $passedEmailData, $passedData);
		}
	}

	//send new user invitation
	function sendNewUserInvitation($DBAccessLib, $UtilityLib, $passedData)
	{

		if ($passedData['invite_user_email'] && $passedData['user_first_name'] && $passedData['user_last_name']) {
			$email_track_id = $UtilityLib->generateId('EMAILTRACK_');
			$header = 'You have been invited to join <b>' . $passedData['project_name'] . '</b> project team !!!';
			$subject = $this->settings['email']['appName'] . ' - You have been invited to join';
			$emailBodyMessage = '
			<tr
				style="
					font-family: Helvetica, Arial, sans-serif;
					font-size: 14px;
					margin: 0;
					padding: 0;
				"
			>
				<td
					style="
						font-family: Helvetica, Arial,
							sans-serif;
						font-size: 14px;
						vertical-align: top;
						margin: 0;
						padding: 0 0 20px;
					"
					valign="top"
				>
					<b>
					' . $passedData['user_first_name'] . ' ' . $passedData['user_last_name'] . '
					</b> has invited you to collaborate in <b>' . $passedData['project_name'] . '</b> project. Use the button below to set up your account and get started !!
					<br /><br />
				</td>
			</tr>

			<tr>
				<td style="text-align: center;">
					<a
						style="background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;border-radius: 8px;font-size: 16px;"
						href="https://c2.rollingarray.co.in/"
						target="_blank" 
					>Set up my account</a>
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<br />
					Feel free to ignore this email if you do not wish to accept the invitation.
					<br />
				</td>
			</tr>

			<tr>
				<td>
					<br />
					Alternatively, Here are few links make you up and running !!
					<ul>
					<li>
						<a target="_blank" href="https://c2.doc.rollingarray.co.in/">C2 in Help & Documentation center</a>
					</li>
					<li>
						<a target="_blank" href="https://c2.doc.rollingarray.co.in/#/go/articles/authentication-&-authorization">User Authentication & Authorization</a>
					</li>
					</ul>
					<br />
				</td>
			</tr>
      ';

			$passedEmailData = array(
				"user_full_name" => 'Patron',
				"email_track_id" => $email_track_id,
				"user_email" => $passedData['invite_user_email'],
				"email_subject" => $subject,
				"email_content" => $emailBodyMessage,
				"email_header" => $header,
			);

			$this->sendEmail($DBAccessLib, $subject, $passedEmailData, $passedData);
		}
	}
}
