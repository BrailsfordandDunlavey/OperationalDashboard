<?php


	global $wpdb;
	include('classes/stem.php');
	include('classes/cleaner.php');
	
	$string = $_POST['queryString']; 
	$stemmer = new Stemmer;
		$stemmed_string = $stemmer->stem ( $string );
	
		$clean_string = new jSearchString();
		$stemmed_string = $clean_string->parseString ( $stemmed_string );		
		
		$new_string = '';
		foreach ( array_unique ( split ( " ",$stemmed_string ) ) as $array => $value )
		{
			if(strlen($value) >= 1)
			{
				$new_string .= ''.$value.' ';
			}
		}
	
	
	$new_string = substr ( $new_string,0, ( strLen ( $new_string ) -1 ) );
	
		$new_string = htmlspecialchars($_POST['queryString']);
			
		if ( strlen ( $new_string ) > 0 ):
		
			$split_stemmed = split ( " ",$new_string );
		        
		    
			$sql = "SELECT DISTINCT COUNT(*) as occurences, ".$wpdb->prefix."posts.post_title FROM ".$wpdb->prefix."posts,
			".$wpdb->prefix."postmeta WHERE ".$wpdb->prefix."posts.post_status='publish' and 
			".$wpdb->prefix."posts.post_type='project' 
			
					AND ".$wpdb->prefix."posts.ID = ".$wpdb->prefix."postmeta.post_id 
					AND ".$wpdb->prefix."postmeta.meta_key = 'closed' 
					AND ".$wpdb->prefix."postmeta.meta_value = '0' 
			
			AND (";
		             
			while ( list ( $key,$val ) = each ( $split_stemmed ) )
			{
		              if( $val!='' && strlen ( $val ) > 0 )
		              {
		              	$sql .= "(".$wpdb->prefix."posts.post_title LIKE '%".$val."%' OR ".$wpdb->prefix."posts.post_content LIKE '%".$val."%') OR";
		              }
			}
			
			$sql=substr ( $sql,0, ( strLen ( $sql )-3 ) );//this will eat the last OR
			$sql .= ") GROUP BY ".$wpdb->prefix."posts.post_title ORDER BY occurences DESC LIMIT 10";
		
	
		
			$query = mysql_query($sql) or die ( mysql_error () );
			//$row_sql = mysql_fetch_assoc ( $query );
			$total = mysql_num_rows ( $query );
			 
			if($total>0):
	
			    
					while ( $row = mysql_fetch_assoc ( $query ) ) 
					{				
						
						
						
						
				echo '<ul>';
				
	         			echo '<li onClick="fill(\''.$row['post_title'].'\');">'.$row['post_title'].'</li>';
	         		
				echo '</ul>';
										
					}
			else:
			
			
			echo '<ul>';
				
	         			echo '<li onClick="fill(\''.$_POST['queryString'].'\');">'.__('No results found','ProjectTheme').'</li>';
	         		
				echo '</ul>';
					
				
			endif;
		endif;

	
	
				
	
	exit;	


?>