<?php
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
 	$employeeid = $_GET['ID'];
						
	?>   
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<div id="content">
				<div class="my_box3">
					<div class="padd10">				
						<ul class="other-dets_m">
							<li>&nbsp;</li>
							<li>
							<table width="100%">
							<tr>
							<th><?php echo "Date";?></th>
							<th><?php echo "Project";?></th>
							<th><?php echo "Hours";?></th>
							<th><?php echo "Notes";?></th>
							</tr>
							<tr>
							<?php 
							$timequery = "select * from ".$wpdb->prefix."timesheets where timesheet_date='$timesheet' and user_id='$uid'";
							$timeresults = $wpdb->get_results($timequery);
							$t = -1;
							foreach ($timeresults as $time)
							{
								$t = $t++;
								$date = date('m-d',$time->timesheet_date);
								echo '<th>'.$date.'</th>';
								echo '<th><select class="do_input_new" name="record['.$t.'][project]"">';

								$queryactive = "select distinct ".$wpdb->prefix."projects.ID,".$wpdb->prefix."projects.client_name,".$wpdb->prefix."projects.project_name,".$wpdb->prefix."projects.gp_id from ".$wpdb->prefix."projects inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
									where ".$wpdb->prefix."project_user.user_id ='$uid' and ".$wpdb->prefix."projects.status =2";								
								$resultactive = $wpdb->get_results($queryactive);
								foreach ($resultactive as $active)
								{
									$p = '<option value="'.$active->ID.'" ';
									if($time->project_id == $active->ID){$p .= 'selected="selected" ';}
									$p .= '>'.$active->gp_id.'</option>';
									echo $p;
								}
							echo '<option>Vacation</option><option>Sick</option><option>Bereav</option><option>Float</option><option>Jury</option><option>Mat/Pat</option>';
							echo '</select></th><th><input type="text" class="do_input_new" value ="'.$time->timesheet_hours.'" size="2" name="record['.$t.'][hours]""/></th>';
							echo '<th><input type="text" class="do_input_new full_wdth_me" value ="'.$time->timesheet_notes.'" name="record['.$t.'][notes]""/></th></tr>';
							
							}?>
							
							</table>
							</li>
							<li>&nbsp;</li>
							<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" />
							
						</ul>	
					

</div>
	
</div> </div>

				</div></div>
				</form>
				
<?php get_footer(); ?>