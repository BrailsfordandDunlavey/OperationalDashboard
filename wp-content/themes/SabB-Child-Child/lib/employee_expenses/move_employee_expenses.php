<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
get_header();
global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;
$user_name = $current_user->display_name;

$exp_id = $_GET['ID'];	

$results = $wpdb->get_results($wpdb->prepare("select gp_id,employee_expense_id,expense_code_name,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,project_id
	from ".$wpdb->prefix."employee_expenses 
	left join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
	inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
	where expense_report_id=%d",$exp_id));


if(isset($_POST['save-info']))
{
	$records = $_POST['record'];
	
	foreach($records as $record)
	{
		$id = $record['details'];
		$old_amount = $record['old_amt'];
		$new_amount  = $record['new_amt'];
		
		
	}
}
?>
	<div class="page_heading_me">
		<div class="page_heading_me_inner">
			<div class="main-pg-title">
				<div class="mm_inn"><?php echo "Move Employee Expenses";?> 
				</div>
			</div>
		</div>
	</div>
	<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="move_exp" enctype="multipart/form-data">
			<div id="content">
			<style>input[type=number]{width:95px;}</style>
				<div class="my_box3">
					<div class="padd10">
					<ul class="other-dets_m">
					<?php
					echo '<li><h3>Move to Project:</h3><p><input type="text" name="new_project" onkeyup="checkProject(this.value);"/>
						<input type="hidden" name="project_id" />
						</p></li>';
					?>
					<span id="project_buttons"></span>
					<script type="text/javascript">
					function checkProject(vals){
						var myForm = document.forms.move_exp;
						var span = document.getElementById('project_buttons');
						span.style.display = 'block';
						jQuery.post("<?php bloginfo('siteurl'); ?>/?check_projects=1", {search_term: ""+vals+""}, function(data){
							if(data.length >0) {
								jQuery('#project_buttons').html(data);
							}
						});
					}
					function setProject(gp_id,id){
						var myForm = document.forms.move_exp;
						var newProject = myForm.elements['new_project'];
						var projectId = myForm.elements['project_id'];
						var span = document.getElementById('project_buttons');
						newProject.value = gp_id;
						projectId.value = id;
						span.style.display = 'none';
					}
					function totalCheck(x){
						var myForm = document.forms.move_exp;
						var submitButton = myForm.elements['save-info'];
						var max = document.getElementById(x);
						var oldProjectAmount = myForm.elements['record[' + x + '][old_amt]'];
						var newProjectAmount = myForm.elements['record[' + x + '][new_amt]'];
						
						if((oldProjectAmount.value*1 + newProjectAmount.value*1)!=max.value*1){
							submitButton.disabled= true;
							alert('Totals do not match, please check and revise accordingly');
							oldProjectAmount.style.backgroundColor = "yellow";
							newProjectAmount.style.backgroundColor = "yellow";
						}
						else{
							submitButton.disabled = false;
							oldProjectAmount.style.backgroundColor = "initial";
							newProjectAmount.style.backgroundColor = "initial";
						}
					}
					</script>
					<?php
					echo '<li><table width="100%">';
					echo '<tr><th><b><u>Project</b></u></th>
							<th><b><u>Expense Type</b></u></th>
							<th><b><u>Expense Total</b></u></th>
							<th><b><u>Amount to Original Project</b></u></th>
							<th><b><u>Amount to New Project</b></u></th>
							</tr>';
					foreach($results as $r)
					{
						echo '<tr><td>'.(empty($r->gp_id) ? $r->project_id : $r->gp_id).'</td>
								<input type="hidden" name="record['.$r->employee_expense_id.'][details]" value="'.$r->employee_expense_id.'" />
								<td>'.$r->expense_code_name.'</td>
								<td>$'.number_format(($r->expense_quantity*$r->expense_amount),2).'</td>
								<input type="hidden" id="'.$r->employee_expense_id.'" value="'.$r->expense_quantity*$r->expense_amount.'" />
								<td><input type="number" step=".01" value="'.$r->expense_quantity*$r->expense_amount.'" 
									min=0 max="'.$r->expense_quantity*$r->expense_amount.'" name="record['.$r->employee_expense_id.'][old_amt]" /></td>
								<td><input type="number" step=".01" 
									min=0 max="'.$r->expense_quantity*$r->expense_amount.'" name="record['.$r->employee_expense_id.'][new_amt]" 
									onblur="totalCheck('.$r->employee_expense_id.');"/></td>
								</tr>';
					}
					echo '</table></li>';
					echo '<li>&nbsp;</li>';
					echo '<li><input type="submit" value="save" name="save-info" class="my-buttons"/></li>';
					
					?>
					</ul>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
<?php get_footer();?>