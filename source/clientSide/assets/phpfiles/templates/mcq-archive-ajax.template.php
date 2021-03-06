
<script>
System.import('<?= \SZN\App::$locations['app']['url'] . '/sharedApp/javascripts' . '/stopImageStealing.js'; ?>');
</script>

<template is="dom-bind">

	<?php
	// chec if payment is made.
	$transactionResponse = SZN\_Specific\PaypalPDT::check_transaction();
	$is_a_member = SZN\WPExtend\Visitor::isCurrentUserInGroup('MCQs');
	?>

<?php
if(($transactionResponse['payment_status'] == 'Completed') && !$is_a_member) {
	$today = getdate();
	$todayDate = $today['mday'] . '/' . ($today['mon']+1) . '/' . $today['year'];

?>
	<template is="dom-bind">
			<style>
				span {
				 color: #808080;
		     line-height: 1.5;
				}
				span.right {
					float: right;
					color: black;
					margin-left: 15px;
			    line-height: 1.5;
				}
			</style>
			<!-- Attention dialog -->
			<paper-dialog id="completed" style="text-align:left;" no-cancel-on-esc-key no-cancel-on-outside-click with-backdrop>
				<h1 style="text-align:center;direction: ltr;">Payment completed</h2>
				<paper-dialog-scrollable>
					<p><span>Purchased Membership:</span> <span class="right"><?= $transactionResponse['item_name']; ?></span></p>
					<p><span>Membership Valid Date:</span> <span class="right">till <?= $todayDate; ?> 12pm</span></p>
					<p><span>Your Email:</span> <span class="right"><?= $transactionResponse['payer_email']; ?></span></p>
					<p><span>Paid Amount:</span> <span class="right"><?= $transactionResponse['mc_gross'] . ' ₪'; ?></span></p>
					<p><span>Transaction Number:</span> <span class="right"><?= $transactionResponse['txn_id']; ?></span></p>
					<!-- Attention about the examination -->
				</paper-dialog-scrollable>
				<div class="buttons">
					<?php
					if( email_exists( $transactionResponse['payer_email'] )) {

						$user = get_user_by( 'email', $transactionResponse['payer_email'] );
						$group_id = 2;
						$user_id = $user->ID;
						Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' => $group_id ) );

						$current_url = WP_HOME . '/' . $_SERVER["REQUEST_URI"]; // get current url of the page.
						$current_url = strtok($current_url, '?');
						$logginglink = wp_login_url($current_url);
					?>
					<p>You already have an account with this email. Please sign-in</p>
					<paper-button dialog-confirm autofocus onclick="window.location.href='<?= $logginglink; ?>'">Sign-In</paper-button>
					<?php
					 } else {

						 // should create an account.

						 $current_url = WP_HOME . '/' . $_SERVER["REQUEST_URI"]; // get current url of the page.
						 $current_url = strtok($current_url, '?');
						 $logginglink = wp_registration_url($current_url);

						 $user_name = $transactionResponse['payer_email'];
						 $user_email = $transactionResponse['payer_email'];
						 $user_id = username_exists( $user_name );
							if ( !$user_id and email_exists($user_email) == false ) {
								$random_password = wp_generate_password( $length=5, $include_standard_special_chars=false );
								$user_id = wp_create_user( $user_name, $random_password, $user_email );
							} else {
								$random_password = __('User already exists.  Password inherited.');
							}
							$group_id = 2;
							Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' => $group_id ) );
					?>

						<paper-button onclick="window.location.href='<?= $logginglink; ?>'" dialog-confirm autofocus>Create an account</paper-button>
					<?php
					 }
					 ?>
					<!-- <paper-button dialog-dismiss>Cancel</paper-button> -->
				</div>
			</paper-dialog>

			<script>
				document.addEventListener('WebComponentsReady', function() {
					setTimeout(function() {
						document.getElementById('completed').open();
					}, 300);
				}, false);
			</script>
		<!-- END Attention about the examination -->
	</template>
<?php
} elseif($transactionResponse == 'Canceled') {
	echo $transactionResponse;
}

?>




<?php
if(is_user_logged_in() && (in_array('administrator', $current_user->roles) || $is_a_member)) {
?>
			<examinations-data examinations="{{examinations}}"></examinations-data>
			<examinations-view examinations="{{examinations}}"></examinations-view>

<?php
} elseif(is_user_logged_in()) {
?>
			<paper-material elevation="3" align="center" style="background-color:white; display: block; text-align:center; max-width: 600px; margin: 15px auto 0 auto; padding: 10px;">
			You don't have enough permissions to view the questions. Please progress with payment.
			</paper-material>
<?php
} else {
?>
			<paper-material elevation="3" align="center" style="background-color:white; display: block; text-align:center; max-width: 600px; margin: 15px auto 0 auto; padding: 10px;">
				<?php
				$current_url = WP_HOME . '/' . $_SERVER["REQUEST_URI"]; // get current url of the page.
				$current_url = strtok($current_url, '?');
				$logginglink = wp_login_url($current_url);

				?>
				<paper-button dialog-confirm autofocus onclick="window.location.href='<?= $logginglink; ?>'">Sign-In</paper-button>
			</paper-material>
			<span style="margin: auto; text-align: center; display: block; margin: 15px auto 15px auto;"> OR </span>
	<?php
	}
?>


<?php
if((is_user_logged_in() && !(in_array('administrator', $current_user->roles) || $is_a_member)) || !is_user_logged_in()) {
?>


		<paper-material elevation="3" align="center" style="background-color:white; display: block; text-align:center; max-width: 600px; margin: 0px auto 40px auto; padding: 10px;">

			<span style="display: block; font-weight:bold; font-size: 18px; margin-top: 10px;">Preparation system for Israeli dental state examination.</span>
			<br>
			<span style="margin-bottom: 20px; text-align: right; display: block;     line-height: 22px;" dir="rtl">האתר מעודכן באופן תמידי, כולל בתוכו יותר מ-1,000 שאלות, מיותר מ-15 שחזורים. התשובות מבוססות על חומרי לימוד שונים (חלקם מאוניברסיטת תל אביב ואונ' ירושלים), על תשובות מקורסי הכנה לבחינה, ולבסוף על תשובות משחזורים קודמים.</span>

			<h1 style="margin: 0 0 5px 0; font-size: 65px;"> 300<span style="font-size: 32px;">₪</span></h1> <span style="color:grey;"> for 3 months (from the time of registration)</span>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="text-align: center; margin-bottom: 6px; margin-top: 20px;">

			<!-- Attention about the examination -->
				<section onclick="clickHandler(event)" style="text-align:center;direction: rtl; margin-bottom: 6px;">

					<paper-button data-dialog="scrolling" align="center" style="margin:auto;direction: rtl; margin-bottom:10px;margin-top:10px;">נא לקרוא !</paper-button>

					<!-- Attention dialog -->
					<paper-dialog id="scrolling" style="text-align:right; z-index: 0; ">
						<h2 style="text-align:center;direction: ltr;">Attention ! Several things to mention:</h2>
						<paper-dialog-scrollable>
							<p style="direction: ltr; text-align:left;">- This is a one time payment (not recurring), granting membership for 3 month. </p>
							<p style="direction: ltr; text-align:left;">- Technical issues: The website is build using new tools which are not supported by IOS devices (i.e. Apple devices), therefore it will not be rendered correctly by IOS. The recommended way to view the website is using Chrome browser.</p>
							<p style="direction: ltr; text-align:left;">- The system is updated regularly, new questions are added, and some old questions are updated.</p>
							<p style="direction: ltr; text-align:left;">- New English questions were added !</p>
							<p style="direction: ltr; text-align:left;">- We do not claim to have correct answers for all the questions, rather offering helpful suggestions guiding you to understanding and choosing the correct answers.</p>
							<br>
							<p>Some old comments to take into consideration:</p>
							<p>1. חלק מהשאלות באתר ממוזגות ממבחני רישוי שונים. ניסוח קצת שונה יכול גם לשנות את התשובה המועדפת. </p>
							<p>2. לחלק מהשאלות במבחן יכול להיות ניסוח מטעה ולא שלם. לשאלה יכולות להיות מספר תשובות נכונות, אך בפועל מוגדרת רק תשובה אחת כנכונה. לפעמיםאחרי ערעור מוסיפים את אחת האופציות כתשובה נכונה ומקובלת בשאלה, ומוסיפים נקודות לכל מי שענה בה.</p>
							<p> 3. המשרד האחראי על מבחני הרישוי מחליט לפעמים להוסיף את אחת האופציות כתשובה נכונה מסיבות של ערעורים על השאלה. ואף מבטל שאלות מסוימות אם ניסוחם מטעה או לא נכון. כך שזה לא 100% נכון או 100% לא נכון.</p>
							<p>	4. בערעורים כותבים את התשובה הלא נכונה. אך גם כאן יש מקום לטעויות מטעם משרד הבריאות או הבוחנים. כל שנה כמעט יש שינויים בחלק מהשאלות ופוסלים חלק. היו מקרים בהם ערעורים שונים נוגדים אחד את השני. חלק גדול מהערעורים המוגשים מתקבלים.</p>
							<p>התשובות באתר מבוססות על שחזורים קודמים, חומר אוניברסיטת ת"א וירושלים, מקורות שונים כמו ספרים ואנטרנט, והגיון, ולבסוף ערעורים.</p>
							<p>כמו שאלה על "הגורם הסיכון המשמעותי ביותר למחלה פריודונטלית היינה ?" הוסיפו לשאלה אחרי ערעורים את התשובה "הגיינה אורלית" כתשובה נכונה חוץ מ- "עישון". כלומר כל מי שסימן הגיינה אורלית גם קיבל נקודות. וחוץ מזה השאלה מוזרה ופתוחה לויכוח.</p>
							<p> למשל השאלה על שחרור מושהה - "מה האנטיביוטיקה שניתנת בשביל שחרור מושהה ? |או| איזה אנטיביוטיקה מופיעה בתכשיר מקומי לשחרור מושהה ? " - יש שתי תשובות נכונות metronidazole ו- Minocycline. </p>
							<p> במבחן האחרון יש שתי תשובות או 3 שבטלו ( 2015פברואר ). ועוד שאלות שהוסיפו להם תשובה מהאופציות הקיימות כתשובה נכונה.</p>
							<p>דוגמא אחרונה היא על שאלה טטראצקלין. התשובה "חודר לפריזמות האמייל ודנטין" נכונה !". שאלה זו התבטלה עקב ערעורים. השאלה הופיעה שוב לאחרונה בניסוח שונה, שבהחלט הופיעה התשובה המוזכרת כתשובה שגויה.</p>


							<p>לפי כך השימוש באתר הוא בהתאם. אין זמן לתקן את כל השאלות ולפעמים אין הסבר לתשובה או אין הגיון פשוט לשנן בעל"פ. נא לזכור יש מקום לטעויות באתר. </p>

							<!-- Attention about the examination -->
						</paper-dialog-scrollable>
						<div class="buttons" style="text-align:center;">
							<!-- <paper-button dialog-dismiss>Cancel</paper-button> -->
							<paper-button dialog-confirm autofocus style="background-color: green; margin-left: auto; margin-right: auto;">I agree & accept</paper-button>
						</div>
					</paper-dialog>
					<script>
						// solves index-z issue with header.
						var dialog = document.querySelector('#scrolling');
						document.getElementsByTagName('body')[0].appendChild(dialog);
					</script>

				</section>
				<!-- END Attention about the examination -->

				<!-- <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="W8UDTE6D435JY">
				<input type="image" src="https://www.sandbox.paypal.com/en_US/IL/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">


				<input type="hidden" name="return" value="<?= WP_HOME . '/' . strtok($_SERVER["REQUEST_URI"],'?'); ?>" />
				<input type="hidden" name="cancel_return" value="<?= WP_HOME . '/' .  strtok($_SERVER["REQUEST_URI"],'?') .  '/' .'?tx=Canceled'; ?>" />
				</form> -->

			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="5M9P7DJFZT9X4">
			<input type="image" src="https://www.paypalobjects.com/en_US/IL/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style=" padding: 0; border: 0; text-align: center;">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">

			<input type="hidden" name="return" value="<?= WP_HOME . '/' . strtok($_SERVER["REQUEST_URI"],'?'); ?>" />
			<input type="hidden" name="cancel_return" value="<?= WP_HOME . '/' .  strtok($_SERVER["REQUEST_URI"],'?') .  '/' .'?tx=Canceled'; ?>" />

			</form>

			<!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center" style="text-align: center; margin:auto;"><tr><td align="center" dir="rtl" style="font-size: 11px;">לתשלום בכרטיס אשראי יש לבחור ב- "Create a PayPal account", ולמלא את הפרטים הנחוצים.</td></tr><tr><td align="center" style="font-size: 11px;">* Payment is handled by PayPal. It can be made using a Credit Card or a PayPal Account.</td></tr><tr><td align="center"><div style="text-align:center; display: none;"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><font size="2" face="Arial" color="#0079CD">How PayPal Works</font></a></div></td></tr></table><!-- PayPal Logo -->
		</paper-material>
		<paper-material elevation="3" align="center" style="background-color:white; display: block; text-align:center; max-width: 600px; margin: 0px auto 20px auto; padding: 10px;">
			If you need any help, please contact me - <a href="https://www.facebook.com/Dr-Safi-Zeidan-Nassar-263203630469965/">Safi Zeidan Nassar</a>
		</paper-material>

		<!-- FREE ACCESS -->
		<!-- <paper-material elevation="3" align="center" style="background-color:#558B2F; display: block; text-align:center; max-width: 1100px; margin: 0px auto 40px auto; padding: 10px;">
			<span style="display: block; font-weight:bold; font-size: 18px; color: white;"><iron-icon icon="icons:offline-pin"></iron-icon>  Limited-Time Partial Free Access !   </span>
		</paper-material>
		<examinations-data examinations="{{examinations}}" examinationnumber="5"></examinations-data>
		<examinations-view examinations="{{examinations}}"></examinations-view> -->
		<!-- END Free Access -->

<?php
}
?>



</template>
