<?php
function billyB_help()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$name = $current_user->display_name;
 
	if(isset($_POST['save-info']))
	{
		?>
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		$comments = '"'.$_POST['comments'].'"';
		
		wp_mail('bbannister@programmanagers.com','Help!',$name.':  '.$comments);
		echo "Thank you for your feedback.  We'll get back to you shortly.<br/><br/>";
		?>
		</div></div></div>

	<?php }	?>
		<form method="post"  enctype="multipart/form-data">
			<div id="content">
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<li>&nbsp;</li>
					<li><h3>I don't see a project listed in my timesheet and/or expense entry</h3>
						<p>Please try to <a href="/add-to-projects/">Add yourself to a Project</a>.  If you still don't see the project listed, it's probably because
							it is not yet setup on the OpDash.  Please let us know what project you're looking for in the form below.
						</p>
					</li>
					<li><h3>Where does my time information go once entered?</h3>
						<p>The time is stored upon clicking "save".  To see the time in daily totals go back to a <a href="/new-timesheet/">New Timesheet</a> and look in the side
						bar for a listing by day (editable) and a detailed listing.  Be sure to select the appropriate Time Period Start at the top.
						</p>
					</li>
					<li><h3>How do I make sure my expense was processed properly?</h3>
						<p>You can review your expense submissions, including what the back up looks like on the <a href="/my-employee-expenses/">My Employee Expenses</a> page.  
						First, make sure your expense was "submitted" by verifying it shows in the "Submitted Expenses" section.  You can see the approval/payment status from here as well.  
						To review the expense report details, click the Report ID - you can see the details and the backup from here.  
						</p>
					</li>
					<li><h3>I need to make edits to an expense report</h3>
						<p>Go to <a href="/new-employee-expense/">New Employee Expense</a>, find the expense report in the appropriate side bar (Unsubmitted/Submitted) and click the "edit" button.
						</p>
					</li>
					<li><h3>Don't see an answer listed here? Fill in this form and let us know</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" 
								placeholder="<?php echo "Please be as descriptive as possible so we can help you quickly"; ?>"  name="comments"></textarea>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
				</ul>
				</div>
				</div>
			</div>
		</form>
<?php 
}
add_shortcode('help','billyB_help')
?>