<?php
/***************************************************************************
*
*	ProjectTheme - copyright (c) - sitemile.com
*	The only project theme for wordpress on the world wide web.
*
*	Coder: Andrei Dragos Saioc
*	Email: sitemile[at]sitemile.com | andreisaioc[at]gmail.com
*	More info about the theme here: http://sitemile.com/products/wordpress-project-freelancer-theme/
*	since v1.2.5.3
*
***************************************************************************/


function ProjectTheme_my_account_payments_area_function()
{
		
		global $current_user, $wpdb, $wp_query;
		get_currentuserinfo(); 
		$uid = $current_user->ID;
		
?>

<div id="content" class="account-main-area">

            
            <?php
			
			$pg = $_GET['pg'];
			if(!isset($pg)) $pg = 'home';

			global $wpdb;
			
			if($_GET['pg'] == 'closewithdrawal')
					{
						$id = $_GET['id'];
						
						$s = "select * from ".$wpdb->prefix."project_withdraw where id='$id' AND uid='$uid'";
						$r = $wpdb->get_results($s);
						
						if(count($r) == 1)
						{
							$row = $r[0];
							$amount = $row->amount;
							
							$cr = projectTheme_get_credits($uid);
							projectTheme_update_credits($uid, $cr + $amount);
							
							$s = "delete from ".$wpdb->prefix."project_withdraw where id='$id' AND uid='$uid'";
							$wpdb->query($s);
							
							echo '<div class="">';
							echo sprintf(__('Request canceled! <a href="%s">Return to payments</a>.','ProjectTheme'), get_permalink(get_option('ProjectTheme_my_account_payments_id')) );	
							echo '</div>';
						}
					}
					
					
					if($_GET['pg'] == 'releasepayment')
					{
						$id = $_GET['id'];
						
						$s = "select * from ".$wpdb->prefix."project_escrow where id='$id' AND fromid='$uid'";
						$r = $wpdb->get_results($s);
						
						if(count($r) == 1 and $r[0]->released != 1)
						{
							$row = $r[0];
							$amount = $row->amount;
							$toid = $row->toid;
							$pid = $row->pid;
							$my_pst = get_post($pid);
							
							$projectTheme_get_winner_bid = projectTheme_get_winner_bid($pid);				
							ProjectTheme_send_email_when_on_completed_project($pid, $projectTheme_get_winner_bid->uid, $projectTheme_get_winner_bid->bid);
							
							//-------------------------------------------------------------------------------
							
							$projectTheme_fee_after_paid = get_option('projectTheme_fee_after_paid');
							if(!empty($projectTheme_fee_after_paid)):
							
								$deducted = $amount*($projectTheme_fee_after_paid * 0.01);
							else: 
							
								$deducted = 0;
							
							endif;
							
							
							//-------------------------------------------------------------------------------
							
							$cr = projectTheme_get_credits($toid);
							projectTheme_update_credits($toid, $cr + $amount - $deducted);
							
							$reason = sprintf(__('Escrow payment received from %s for the project <b>%s</b>','ProjectTheme'), $current_user->user_login, $my_pst->post_title);
							projectTheme_add_history_log('1', $reason, $amount, $toid, $uid);
							
							
							if($deducted > 0)
							$reason = sprintf(__('Payment fee for project %s','ProjectTheme'), $my_pst->post_title);
							projectTheme_add_history_log('0', $reason, $deducted, $toid );
							
							//-----------------------------
							$email 		= get_bloginfo('admin_email');
							$site_name 	= get_bloginfo('name');
							
							$usr = get_userdata($uid);
							
							$subject = __("Money Escrow Completed",'ProjectTheme');
							$message = sprintf(__("You have released the escrow of: %s","ProjectTheme"), ProjectTheme_get_show_price($amount));
	
							//($usr->user_email, $subject , $message);
							
							//-----------------------------
							
							$usr = get_userdata($toid);
							
							$reason = sprintf(__('Escrow Payment completed, sent to %s for project <b>%s</b>','ProjectTheme'), $usr->user_login, $my_pst->post_title);
							projectTheme_add_history_log('0', $reason, $amount, $uid, $toid);
							
							$subject = __("Money Escrow Completed","ProjectTheme");
							$message = sprintf(__("You have received the amount of: %s","ProjectTheme"), ProjectTheme_get_show_price($amount));
	
							//($usr->user_email, $subject , $message);
							
							//-----------------------------
							$tm = current_time('timestamp',0);
							
							update_post_meta($pid,'paid_user','1');
							update_post_meta($pid,'paid_user_date',current_time('timestamp',0));
							
							$s = "update ".$wpdb->prefix."project_escrow set released='1', releasedate='$tm' where id='$id'";
							$r = $wpdb->query($s);
						
							
						}
						
						echo __('Escrow completed! Redirecting...','ProjectTheme'); echo '<br/><br/>';	
							
						$url_redir = ProjectTheme_get_payments_page_url();
						echo '<meta http-equiv="refresh" content="2;url='.$url_redir.'" />';
					
					}
			
			do_action('ProjectTheme_before_payments_in_payments');		
					
			$ProjectTheme_enable_credits_wallet = get_option('ProjectTheme_enable_credits_wallet');
			if($ProjectTheme_enable_credits_wallet != 'no'):
			
			if($pg == 'home'):
			
			
				
			
			?>
            
            
            
            <div class="my_box3">
            
            
            	<div class="box_title"><?php _e("Finances","ProjectTheme"); ?></div>
            	<div class="box_content">
                
                
                
                <?php
				$bal = projectTheme_get_credits($uid);
				echo '<span class="balance">'.__("Your Current Balance is", "ProjectTheme").": ".ProjectTheme_get_show_price($bal,2)."</span>"; 
				
				
				?> 
    
    
               
            </div>
            </div>
            
            <div class="clear10"></div>
            
            <div class="my_box3">
           
            
            	<div class="box_title"><?php _e('What do you want to do','ProjectTheme'); ?></div>
            	<div class="box_content">
                
                <ul class="cms_cms">
                
               <li> <a href="<?php echo ProjectTheme_get_payments_page_url('deposit'); ?>" class="green_btn old_mm_k"><?php _e('Deposit Money','ProjectTheme'); ?></a>  </li>
              <li>  <a href="<?php echo ProjectTheme_get_payments_page_url('makepayment'); ?>" class="green_btn old_mm_k"><?php _e('Make Payment','ProjectTheme'); ?></a> </li>
                
                <?php if(ProjectTheme_is_user_business($uid)): ?>
               <li> <a href="<?php echo ProjectTheme_get_payments_page_url('escrow'); ?>" class="green_btn old_mm_k"><?php _e('Deposit Escrow','ProjectTheme'); ?></a> </li> 
                <?php endif; ?>
                
               <li> <a href="<?php echo ProjectTheme_get_payments_page_url('withdraw'); ?>" class="green_btn old_mm_k"><?php _e('Withdraw Money','ProjectTheme'); ?></a> </li> 
               <li> <a href="<?php echo ProjectTheme_get_payments_page_url('transactions'); ?>" class="green_btn old_mm_k"><?php _e('Transactions','ProjectTheme'); ?></a></li>
               <li> <a href="<?php echo ProjectTheme_get_payments_page_url('bktransfer'); ?>" class="green_btn old_mm_k"><?php _e('Bank Transfer Details','ProjectTheme'); ?></a>   </li> 
    
                  <?php do_action('ProjectTheme_financial_buttons_main') ?>
              
              	</ul>
              
            </div>
            </div>
            
            <!-- ###################### -->
                        <div class="clear10"></div>
            
            <div class="my_box3">
            
            
            	<div class="box_title"><?php _e('Pending Withdrawals','ProjectTheme'); ?></div>
            	<div class="box_content">
               
                
         				<?php
				
					global $wpdb;
					
					//----------------
				
					$s = "select * from ".$wpdb->prefix."project_withdraw where done='0' and rejected!='1' AND uid='$uid' order by id desc";
					$r = $wpdb->get_results($s);
					
					if(count($r) == 0) echo __('No withdrawals pending yet.','ProjectTheme');
					else
					{
						echo '<table width="100%">';
						foreach($r as $row) // = mysql_fetch_object($r))
						{

							
							echo '<tr>';
							echo '<td>'.date_i18n('d-M-Y H:i:s', $row->datemade).'</td>';
							echo '<td>'.ProjectTheme_get_show_price($row->amount).'</td>';
							echo '<td>'.$row->methods .'</td>';
							echo '<td>'.$row->payeremail .'</td>';
							echo '<td><a href="'.ProjectTheme_get_payments_page_url('closewithdrawal', $row->id).'"
							class="green_btn">'.__('Close Request','ProjectTheme'). '</a></td>';
							echo '</tr>';
							
							
						}
						echo '</table>';
						
					}
				
				?>
                  
               
            </div>
            </div>
            
            
             <div class="clear10"></div>
            
            <div class="my_box3">
            
            
            	<div class="box_title"><?php _e('Rejected Withdrawals','ProjectTheme'); ?></div>
            	<div class="box_content">
               
                
         				<?php
				
					global $wpdb;
					
					//----------------
				
					$s = "select * from ".$wpdb->prefix."project_withdraw where done='0' and rejected='1' AND uid='$uid' order by id desc";
					$r = $wpdb->get_results($s);
					
					if(count($r) == 0) echo __('No withdrawals pending yet.','ProjectTheme');
					else
					{
						echo '<table width="100%">';
						foreach($r as $row) // = mysql_fetch_object($r))
						{

							
							echo '<tr>';
							echo '<td>'.date_i18n('d-M-Y H:i:s', $row->datemade).'</td>';
							echo '<td>'.ProjectTheme_get_show_price($row->amount).'</td>';
							echo '<td>'.$row->methods .'</td>';
							echo '<td>'.$row->payeremail .'</td>';
							echo '<td> </td>';
							echo '</tr>';
							
							
						}
						echo '</table>';
						
					}
				
				?>
                  
               
            </div>
            </div>
            
            
           <!-- ###################### -->
                        <div class="clear10"></div>
            
            <div class="my_box3">
            
            
            	<div class="box_title"><?php _e("Pending Incoming Payments","ProjectTheme"); ?></div>
            	<div class="box_content">
                
                
   				<?php
				
					$s = "select * from ".$wpdb->prefix."project_escrow where released='0' AND toid='$uid' order by id desc";
					$r = $wpdb->get_results($s);
					
					if(count($r) == 0) echo __('No payments pending yet.','ProjectTheme');
					else
					{
						echo '<table width="100%">';
						foreach($r as $row) // = mysql_fetch_object($r))
						{
							$post = get_post($row->pid);
							$from = get_userdata($row->fromid);
							
							echo '<tr>';
							echo '<td>'.$from->user_login.'</td>';
							echo '<td>'.$post->post_title.'</td>';
							echo '<td>'.date_i18n('d-M-Y H:i:s', $row->datemade).'</td>';
							echo '<td>'.ProjectTheme_get_show_price($row->amount).'</td>';
							
							echo '</tr>';
							
							
						}
						echo '</table>';
						
					}
				
				?>
                  
                
            </div>
            </div>
         
         
                    <!-- ###################### -->
                   
                   <?php if(ProjectTheme_is_user_business($uid)): ?>
                   
                        <div class="clear10"></div>
            
            <div class="my_box3">
            
            
            	<div class="box_title"><?php _e('Pending Outgoing Payments','ProjectTheme'); ?></div>
            	<div class="box_content">
                
                
      				<?php
				
					$s = "select * from ".$wpdb->prefix."project_escrow where released='0' AND fromid='$uid' order by id desc";
					$r = $wpdb->get_results($s);
					
					if(count($r) == 0) echo __('No payments pending yet.','ProjectTheme');
					else
					{
						echo '<table width="100%">';
						
						echo '<tr>';
							echo '<td><b>'.__('User','ProjectTheme').'</b></td>';
							echo '<td><b>'.__('Project','ProjectTheme').'</b></td>';
							echo '<td><b>'.__('Date','ProjectTheme').'</b></td>';
							echo '<td><b>'.__('Amount','ProjectTheme').'</b></td>';
							echo '<td><b>'.__('Options','ProjectTheme').'</b></td>';
							
							echo '</tr>';
							
						
						foreach($r as $row) // = mysql_fetch_object($r))
						{
							$post = get_post($row->pid);
							$from = get_userdata($row->toid);
							
							echo '<tr>';
							echo '<td><a href="'.ProjectTheme_get_user_profile_link($from->ID).'">'.$from->user_login.'</a></td>';
							echo '<td><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></td>';
							echo '<td>'.date_i18n('d-M-Y H:i:s', $row->datemade).'</td>';
							echo '<td>'.ProjectTheme_get_show_price($row->amount).'</td>';
							echo '<td><a href="'.ProjectTheme_get_payments_page_url('releasepayment', $row->id).'" class="green_btn">'.__('Release Payment','ProjectTheme').'</a></td>';
							
							echo '</tr>';
							
							
						}
						echo '</table>';
						
					}
				
				?>
                  
               
            </div>
            </div> <?php endif; ?>
        <?php
			elseif($pg == 'escrow'):
		?>
        
        
        <div class="my_box3">
           
            
            	<div class="box_title"><?php _e('Make Escrow Payment','ProjectTheme'); ?></div>
            	<div class="box_content">
              
                
                
                <?php
						
				$bal = projectTheme_get_credits($uid);
				
				
				if(isset($_POST['escrowme']))
				{
					$amount 	= $_POST['amount'];
					$projects 	= $_POST['projectss'];
					
					if(!is_numeric($amount) || $amount < 0)
					{
						echo '<div class="newproject_error">'.__('Provide a well formated amount.','ProjectTheme').'</div>';
							
					}
					else if(empty($projects))
					{
						echo '<div class="newproject_error">'.__('Please choose an project.','ProjectTheme').'</div>';	
					}
					else
					{
						if($bal < $amount) 
						{
							echo '<div class="newproject_error">'.__('Your balance is smaller than the amount requested.','ProjectTheme').'</div>';
						}
						else
						{
							$post 	= get_post($projects);
							$uid2   = get_post_meta($projects, "winner", true);
							
							$tm = $_POST['tm'];
							if(empty($tm)) $tm = current_time('timestamp',0);
							
							
							if($post->post_author != $uid)
								$uid2 = $post->post_author;	


							
						
											
							//-----------------------
							$email 		= get_bloginfo('admin_email');
							$site_name 	= get_bloginfo('name');
							
							$usr = get_userdata($uid);
							
							$subject = __("Money Escrow Sent","ProjectTheme");
							$message = sprintf(__("You have placed in escrow the amount of: %s to user: 
							<b>%s</b>","ProjectTheme"),ProjectTheme_get_show_price($amount) ,$username);
	
							//($usr->user_email, $subject , $message);
							
							$s = "select * from ".$wpdb->prefix."project_escrow where datemade='$tm' and fromid='$uid'";
							$rr = $wpdb->get_results($s);
							
							if(count($rr) == 0)
							{
							
								$s = "insert into ".$wpdb->prefix."project_escrow (datemade, amount, fromid, toid, pid) 
								values('$tm','$amount','$uid','$uid2','$projects')";
								$wpdb->query($s);
								
									// for logged in user, the user who sends
									//======================================================
									$cr = projectTheme_get_credits($uid);
									projectTheme_update_credits($uid, $cr - $amount);
							
							}
							//======================================================
							
							// for other user, the user who receives
							//======================================================
							
										
												
							$usr2 = get_userdata($uid2);
							
							$subject = __("Money Escrow Received","ProjectTheme");
							$message = sprintf(__("You have received in escrow the amount of: %s from user: <b>%s</b>","ProjectTheme"),
							ProjectTheme_get_show_price($amount),$usr->user_login);
	
							//($usr2->user_email, $subject , $message);
							
							
							//======================================================
							
							echo '<div class="saved_thing">'.__('Your payment has been sent. Redirecting...','ProjectTheme').'</div>';
							$url_redir = get_permalink(get_option('ProjectTheme_my_account_payments_id'));
							echo '<meta http-equiv="refresh" content="2;url='.$url_redir.'" />';
						}
						
					}
					
				}
				
				
				$bal = projectTheme_get_credits($uid);
				echo '<span class="balance">'.sprintf(__('Your Current Balance is: %s','ProjectTheme'), ProjectTheme_get_show_price($bal))."</span>";
				echo '&nbsp; <a class="post_bid_btn" href="'.ProjectTheme_get_payments_page_url_redir('deposit').'">'.__('Add More Credits','ProjectTheme').'</a>';
				echo "<br/><br/>"; 
				
				?>
                
                <script>
				
				function on_proj_sel()
				{
					var sel_value = jQuery("#my_proj_sel").val();
					
					$.post("<?php bloginfo('siteurl'); ?>/?get_my_project_vl_thing=1", {queryString: ""+sel_value+""}, function(data){
						if(data.length >0) {
							
							var currency = '<?php echo ProjectTheme_get_currency() ?>';
							jQuery("#my_escrow_amount").html(currency  + data);
							jQuery("#amount").val(data);
					
							
						}
					});
					
			 
					
				}
				
				<?php 
					
					if(!empty($_GET['poid']))
					{
						?>
						jQuery(function() {
							  on_proj_sel();
							});
						
						<?php
					}
				
				?>
				
				
				</script>
                
                
    				<br /><br />
                    <table>
                    <form method="post" action="">
                    <input type="hidden" value="<?php echo current_time('timestamp',0) ?>" name="tm" />
                    <tr>
                    <td width="150"><?php _e('Escrow amount','ProjectTheme'); ?>:</td><td> <input value="0" type="hidden" 
                    size="10" name="amount" id="amount" /> <span id="my_escrow_amount"><?php _e('Select your project.','ProjectTheme'); ?></span></td>
                    </tr>
                    <tr>
                    <td><?php _e('Escrow for Project','ProjectTheme'); ?>:</td><td> <?php $st = ProjectTheme_get_my_awarded_projects($uid);
					if($st == false) echo '<strong>'.__('You dont have any awarded projects.','ProjectTheme').'</strong>'; else echo $st;?></td>
                    </tr>
                    
                    <tr>
                    <td></td>
                    <td>
                    <input type="submit" name="escrowme" value="<?php _e('Make Escrow','ProjectTheme'); ?>" /></td></tr></form></table>
    
              
            </div>
            </div> 
        
        
        
        <?php
			elseif($pg == 'bktransfer'):
		?>
        
        
        <div class="my_box3">
        
            
            	<div class="box_title"><?php _e('Set your Bank Transfer Details','ProjectTheme'); ?></div>
            	<div class="box_content">
                
                
                
                <?php
						
				$bal = projectTheme_get_credits($uid);
				
				
				if(isset($_POST['bank_details']))
				{
					$bank_details 	= $_POST['bank_details'];
					update_user_meta($uid, 'bank_details', $bank_details);
					echo __("Saved","ProjectTheme");
					
				}
				
	
				?>
    				<br /><br />
                    <table>
                    <form method="post">
                    <tr>
                    <td valign="top"><?php _e("Bank details","ProjectTheme"); ?>:</td>
                    <td> <textarea cols="60" name="bank_details" rows="6"><?php echo get_user_meta($uid,'bank_details',true); ?></textarea></td>
                    </tr>
                  
                    
                    <tr>
                    <td></td>
                    <td>
                    <input type="submit" name="submit" value="<?php _e("Save Details","ProjectTheme"); ?>" /></td></tr></form></table>
    			
                  
            </div>
            </div> 
        
        
        
        <?php
			elseif($pg == 'makepayment'):
			
			
		?>
        
          <div class="my_box3">
           
            
            	<div class="box_title"><?php echo __("Make Payment","ProjectTheme"); ?></div>
            	<div class="box_content">
             
                
                
                <?php
						
				$bal = projectTheme_get_credits($uid);
				
				
				if(isset($_POST['payme']))
				{
					$amount 	= $_POST['amount'];
					$username 	= $_POST['username'];
					
					$username_select 	= $_POST['projectss'];
					
					if(!is_numeric($amount) || $amount < 0)
					{
						echo '<div class="newproject_error">'.__('ERROR: Provide a well formated amount.','ProjectTheme').'</div>';
							
					}
					else if(projectTheme_username_is_valid($username) == false && empty($username_select))
					{
						echo '<div class="newproject_error">'.__('ERROR: Invalid username provided.','ProjectTheme').'</div>';	
					}
					
					else if($username == $current_user->user_login)
					{
						echo '<div class="newproject_error">'.__('ERROR: You cannot transfer money to your own account.','ProjectTheme').'</div>';	
					}
					else
					{
						$min = get_option('project_theme_transfer_limit');
						if(empty($min)) $min = 20;
					
						if($bal < $amount) 
						{
							echo '<div class="newproject_error">'.__('ERROR: Your balance is smaller than the amount requested.','ProjectTheme').'</div>';
						}
						else if($amount < $min)
						{
							echo '<div class="newproject_error">'.sprintf(__('ERROR: The amount should not be less than %s','ProjectTheme'), ProjectTheme_get_show_price($min)).'.</div>';
						}
						else
						{
							$tm = current_time('timestamp',0);
							$uid2 = projectTheme_get_userid_from_username($username);
							$usr2 = get_userdata($uid2);
							
							if(!empty($username_select)) { $uid2 = $username_select; $username = get_userdata($uid2); $username = $username->user_login; }
							
							// for logged in user, the user who sends
							//======================================================
							$cr = projectTheme_get_credits($uid);
							projectTheme_update_credits($uid, $cr - $amount);
											
							//-----------------------
							$email 		= get_bloginfo('admin_email');
							$site_name 	= get_bloginfo('name');
							
							$usr = get_userdata($uid);
							
							$subject = __("Money Sent","ProjectTheme");
							$message = sprintf(__("You have sent amount of: %s to user: <b>%s</b>","ProjectTheme")
							,ProjectTheme_get_show_price($amount),$usr2->user_login);
	
							//($usr->user_email, $subject , $message);
							
							$reason = sprintf(__("Amount transfered to user %s","ProjectTheme"), $usr2->user_login);
							projectTheme_add_history_log('0', $reason, $amount, $uid, $uid2);
							
							//======================================================
							
							// for other user, the user who receives
							//======================================================
							
							$cr = projectTheme_get_credits($uid2);
							projectTheme_update_credits($uid2, $cr + $amount);
											
												
							
							
							$subject = __("Money Received","ProjectTheme");
							$message = sprintf(__("You have received amount of: %s from user: <b>%s</b>","ProjectTheme"),
							ProjectTheme_get_show_price($amount),$usr->user_login);
	
							//($usr2->user_email, $subject , $message);
							
							$reason = sprintf(__("Amount transfered from user %s","ProjectTheme"), $usr->user_login);
							projectTheme_add_history_log('1', $reason, $amount, $uid2, $uid);
							
							//======================================================
							
							echo '<div class="saved_thing">'.__('Your payment has been sent. Redirecting...','ProjectTheme').'</div>';
							$url_redir = get_permalink(get_option('ProjectTheme_my_account_payments_id'));
							echo '<meta http-equiv="refresh" content="2;url='.$url_redir.'" /><br/>';
						}
						
					}
					
				}
				
				global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
				$bal = projectTheme_get_credits($uid);
				echo '<span class="balance">'. sprintf(__("Your Current Balance is %s",""), ProjectTheme_get_show_price($bal)).":</span><br/><br/>"; 
				
				?>
    				<br /><br />
                    <table>
                    <form method="post" enctype="application/x-www-form-urlencoded">
                    <tr>
                    <td><?php echo __("Payment amount","ProjectTheme"); ?>:</td>
                    <td> <input value="<?php echo $_POST['amount']; ?>" type="text" 
                    size="10" name="amount" /> <?php echo projectTheme_currency(); ?></td>
                    </tr>
                    <tr>
                    <td><?php echo __("Pay to user","ProjectTheme"); ?>:</td>
                    <td><input value="<?php echo $_POST['username']; ?>" type="text" size="30" name="username" /> 
					
					
					<?php 
					
					$trg = ProjectTheme_get_my_awarded_projects2($uid);
					
					if($trg) { _e('or','ProjectTheme')." &nbsp; "; 
					echo ProjectTheme_get_my_awarded_projects2($uid); } ?></td>
                    </tr>
                    
                    <tr>
                    <td></td>
                    <td>
                    <input type="submit" name="payme" value="<?php echo __("Make Payment","ProjectTheme"); ?>" /></td></tr></form></table>
    
              
            </div>
            </div> 
        
              
        <?php    
            elseif($pg == 'withdraw'):	
			
		?>
        
        
               <div class="my_box3">
         
            	<div class="box_title"><?php _e("Request Withdrawal","ProjectTheme"); ?></div>
            	<div class="box_content">
               
                
                
                <?php
						
				$bal = projectTheme_get_credits($uid);
				echo '<span class="balance">';
				printf(__('Your Current Balance is: %s','ProjectTheme'), ProjectTheme_get_show_price($bal)); 
				echo "</span><br/><br/>"; 
				
				do_action('ProjectTheme_add_new_withdraw_posts');
				
				if(isset($_POST['withdraw']) or isset($_POST['withdraw2']) or isset($_POST['withdraw3']))
				{
					$amount = $_POST['amount'];
					$paypal = $_POST['paypal'];
					$meth = $_POST['meth'];
					
					if(isset($_POST['withdraw2']))
					{
						
						$amount = $_POST['amount2'];
						$paypal = $_POST['paypal2'];
						$meth = $_POST['meth2'];	
						
					}
					
					if(isset($_POST['withdraw3']))
					{
						
						$amount = $_POST['amount3'];
						$paypal = $_POST['paypal3'];
						$meth = $_POST['meth3'];	
						
					}
					
					
					if(!is_numeric($amount) || $amount < 0)
					{
						echo '<br/><span class="newproject_error">'.__('Provide a well formated amount.','ProjectTheme').'</span><br/>';
							
					}
					else if(project_isValidEmail($paypal) == false)
					{
						echo '<br/><span class="newproject_error">'.__('Invalid email provided.','ProjectTheme').'</span><br/>';	
					}
					else
					{
						$min = get_option('project_theme_min_withdraw');
						if(empty($min)) $min = 25;
					
						if($bal < $amount) 
						{
							echo '<br/><span class="newproject_error">'.__('Your balance is smaller than the amount requested.','ProjectTheme').'</span><br/>';
						}
						else if($amount < $min)
						{
							echo '<br/><span class="newproject_error">'.sprintf(__('The amount should not be less than %s','ProjectTheme'),
							projecttheme_get_show_price($min)).'.</span><br/>';
						}
						else
						{
							$tm = current_time('timestamp',0); 
							global $wpdb; $wpdb->show_errors = true;
							
							if(!empty($_POST['tm']))
							{
								$tm = $_POST['tm']; //current_time('timestamp',0); 		
							}
							
							$s = "select * from ".$wpdb->prefix."project_withdraw where uid='$uid' and datemade='$tm' ";
							$r = $wpdb->get_results($s);
							
							if(count($r) == 0)
							{
							
								$s = "insert into ".$wpdb->prefix."project_withdraw (methods, payeremail, amount, datemade, uid, done) 
								values('$meth','$paypal','$amount','$tm','$uid','0')";
								$wpdb->query($s);
								
								
								
								if(!empty($wpdb->last_error)) { echo $wpdb->last_error; exit; }
								
								$cr = projectTheme_get_credits($uid);
								projectTheme_update_credits($uid, $cr - $amount);
							
							}
							
							
							
							//-----------------------
							$email 		= get_bloginfo('admin_email');
							$site_name 	= get_bloginfo('name');
							
							$usr = get_userdata($uid);
							
							$subject = __("Money Withdraw Requested","ProjectTheme");
							$message = sprintf(__("You have requested a new withdrawal of: %s","ProjectTheme"), $amount." ".projectTheme_currency());
	
							//($usr->user_email, $subject , $message);
							
							//-----------------------
							
							echo '<div class="saved_thing">'.__('Your request has been queued. Redirecting...','ProjectTheme').'</div>';
							$url_redir = get_permalink(get_option('ProjectTheme_my_account_payments_id'));
							echo '<meta http-equiv="refresh" content="2;url='.$url_redir.'" />';
						}
						
					}
					
				}
				
				global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
					$opt = get_option('ProjectTheme_paypal_enable');
					if($opt == "yes"):
					
				?>
    				<br /><br />
                    <table>
                    <form method="post" enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="meth" value="PayPal" />
                    <input type="hidden" name="tm" value="<?php echo current_time('timestamp',0) ?>" />
                    <tr>
                    <td><?php echo __("Withdraw amount","ProjectTheme"); ?>:</td>
                    <td> <input value="<?php echo $_POST['amount']; ?>" type="text" 
                    size="10" name="amount" /> <?php echo projectTheme_currency(); ?></td>
                    </tr>
                    <tr>
                    <td><?php echo __("PayPal Email","ProjectTheme"); ?>:</td>
                    <td><input value="<?php echo get_user_meta($uid, 'paypal_email',true); ?>" type="text" size="30" name="paypal" /></td>
                    </tr>
                    
                    <tr>
                    <td></td>
                    <td>
                    <input type="submit" name="withdraw" value="<?php echo __("Withdraw","ProjectTheme"); ?>" /></td></tr></form></table>
                    
                    <?php
					endif;
					
					$opt = get_option('ProjectTheme_moneybookers_enable');
					if($opt == "yes"):
					
					?>
                        <br /><br />
                        <table>
                        <form method="post" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="meth2" value="Moneybookers" />
                        <input type="hidden" name="tm" value="<?php echo current_time('timestamp',0) ?>" />
                        <tr>
                        <td><?php echo __("Withdraw amount","ProjectTheme"); ?>:</td>
                        <td> <input value="<?php echo $_POST['amount2']; ?>" type="text" 
                        size="10" name="amount2" /> <?php echo projectTheme_currency(); ?></td>
                        </tr>
                        <tr>
                        <td><?php echo __("Moneybookers Email","ProjectTheme"); ?>:</td>
                        <td><input value="<?php echo get_user_meta($uid, 'moneybookers_email',true); ?>" type="text" size="30" name="paypal2" /></td>
                        </tr>
                        
                        <tr>
                        <td></td>
                        <td>
                        <input type="submit" name="withdraw2" value="<?php echo __("Withdraw","ProjectTheme"); ?>" /></td></tr></form></table>
    				
					<?php endif; 
					
					
					$opt = get_option('ProjectTheme_alertpay_enable');
					if($opt == "yes"):
					
					?>
                        <br /><br />
                        <table>
                        <form method="post" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="meth3" value="Payza" />
                        <tr>
                        <td><?php echo __("Withdraw amount","ProjectTheme"); ?>:</td>
                        <td> <input value="<?php echo $_POST['amount3']; ?>" type="text" 
                        size="10" name="amount3" /> <?php echo projectTheme_currency(); ?></td>
                        </tr>
                        <tr>
                        <td><?php echo __("Payza Email","ProjectTheme"); ?>:</td>
                        <td><input value="<?php echo get_user_meta($uid, 'payza_email',true); ?>" type="text" size="30" name="paypal3" /></td>
                        </tr>
                        
                        <tr>
                        <td></td>
                        <td>
                        <input type="submit" name="withdraw3" value="<?php echo __("Withdraw","ProjectTheme"); ?>" /></td></tr></form></table>
    				
					<?php endif; ?>
					
					
               <?php do_action('ProjectTheme_add_new_withdraw_methods'); ?>	
               
            </div>
            </div>
            
        
            
        <?php    
            elseif($pg == 'deposit'):	
			
			global $USERID;
			$USERID = $uid;
		?>
        
        
    
        <div class="my_box3">
            
            
            	<div class="box_title"><?php _e('Deposit Money','ProjectTheme'); ?></div>
            	<div class="box_content">
                
                <?php
				
				$ProjectTheme_bank_details_enable = get_option('ProjectTheme_bank_details_enable');
				if($ProjectTheme_bank_details_enable == "yes"):
				
				?>
                
                <strong><?php _e('Deposit money by Bank Transfer','ProjectTheme'); ?></strong><br/><br/>
                
                <?php echo get_option('ProjectTheme_bank_details_txt'); ?>
    			<br/><br/>
                <?php endif; ?>
                
                
            	<?php
				
				$ProjectTheme_paypal_enable = get_option('ProjectTheme_paypal_enable');
				if($ProjectTheme_paypal_enable == "yes"):
				
				?>
                
                <strong><?php _e('Deposit money by PayPal','ProjectTheme'); ?></strong><br/><br/>
                
                <form method="post" action="<?php bloginfo('siteurl'); ?>/?p_action=paypal_deposit_pay">
                <?php _e("Amount to deposit:","ProjectTheme"); ?> <input type="text" size="10" name="amount" /> <?php echo projectTheme_currency(); ?>
                &nbsp; &nbsp; <input type="submit" name="deposit" value="<?php _e('Deposit','ProjectTheme'); ?>" /></form>
    			<br/><br/>
                <?php endif; ?>
                <!-- ################## -->
                
                <?php
				
				$ProjectTheme_alertpay_enable = get_option('ProjectTheme_alertpay_enable');
				if($ProjectTheme_alertpay_enable == "yes"):
				
				?>
                
                <strong><?php _e('Deposit money by Payza','ProjectTheme'); ?></strong><br/><br/>
                
                <form method="post" action="<?php bloginfo('siteurl'); ?>/?p_action=payza_deposit_pay">
                <?php _e("Amount to deposit:","ProjectTheme"); ?> <input type="text" size="10" name="amount" /> <?php echo projectTheme_currency(); ?>
                &nbsp; &nbsp; <input type="submit" name="deposit" value="<?php _e('Deposit','ProjectTheme'); ?>" /></form>
    			<br/><br/>
                <?php endif; ?>
                
                
                
                <?php
				
				$ProjectTheme_moneybookers_enable = get_option('ProjectTheme_moneybookers_enable');
				if($ProjectTheme_moneybookers_enable == "yes"):
				
				?>
                
                
                <strong><?php _e('Deposit money by Moneybookers','ProjectTheme'); ?></strong><br/><br/>
                
                <form method="post" action="<?php bloginfo('siteurl'); ?>/?p_action=mb_deposit_pay">
                <?php _e("Amount to deposit:","ProjectTheme"); ?>  <input type="text" size="10" name="amount" /> <?php echo projectTheme_currency(); ?>
                &nbsp; &nbsp; <input type="submit" name="deposit" value="<?php _e('Deposit','ProjectTheme'); ?>" /></form>
    			<br/><br/>
                <?php endif; ?>
                
    			<?php do_action('ProjectTheme_deposit_methods', $uid); ?>
               
            </div>
            </div>
        
        <?php    
            elseif($pg == 'transactions'):	
			
		?>	
		
        		
            <div class="my_box3">
            
            
            	<div class="box_title"><?php _e('Payment Transactions','ProjectTheme'); ?> </div>
            	<div class="box_content">
            
                
                <?php
				
					$s = "select * from ".$wpdb->prefix."project_payment_transactions where uid='$uid' order by id desc";
					$r = $wpdb->get_results($s);
					
					if(count($r) == 0) echo __('No activity yet.','ProjectTheme');
					else
					{
						$i = 0;
						echo '<table width="100%" cellpadding="5">';
						foreach($r as $row) // = mysql_fetch_object($r))
						{
							if($row->tp == 0){ $class="redred"; $sign = "-"; }
							else { $class="greengreen"; $sign = "+"; }
							
							echo '<tr style="background:'.($i%2 ? "#f2f2f2" : "#f9f9f9").'" >';
							echo '<td>'.$row->reason.'</td>';
							echo '<td width="25%">'.date_i18n('d-M-Y H:i:s',$row->datemade).'</td>';
							echo '<td width="20%" class="'.$class.'"><b>'.$sign.ProjectTheme_get_show_price($row->amount).'</b></td>';
							
							echo '</tr>';
							$i++;
						}
						
						echo '</table>';
						
						
					}
				
				?>
    
                 
            </div>
            </div>
        <?php endif; endif; ?>
            
            
                
        </div> <!-- end dif content -->
        
        <?php ProjectTheme_get_users_links(); ?>
        
    
	
<?php	
} 


?>