<?php
if ( ! defined('EXT')) { exit('Invalid file request'); }

/*
=============================================================
	View counts
	- Nathan Pitman, ninefour.co.uk/labs
-------------------------------------------------------------
	Copyright (c) 2009 Nine Four Ltd
=============================================================
	File:			ext.view_counts.php
=============================================================
	Version:		1.0.0
-------------------------------------------------------------
	Compatibility:	EE 1.6.x
-------------------------------------------------------------
	Purpose:		Show the view counts for entries in the
					edit page list.				
=============================================================
*/

class View_counts
{
	var $settings		= array();
	var $name           = 'View Counts';
	var $version        = '1.0';
	var $description    = 'Show the view counts for entries in the edit page list.';
	var $settings_exist = 'n';
	var $docs_url       = 'http://ninefour.co.uk/labs';

	// --------------------------------
	//  Activate Extension
	// --------------------------------
	function activate_extension()
	{
		global $DB, $PREFS;
		
		$sql[] = $DB->insert_string( 'exp_extensions', 
			array('extension_id' 	=> '',
				'class'			=> get_class($this),
				'method'		=> "view_counts_additional_tableheader",
				'hook'			=> "edit_entries_additional_tableheader",
				'settings'		=> '',
				'priority'		=> 10,
				'version'		=> $this->version,
				'enabled'		=> "y"
			)
		);
		$sql[] = $DB->insert_string( 'exp_extensions', 
			array('extension_id' 	=> '',
				'class'			=> get_class($this),
				'method'		=> "view_counts_additional_celldata",
				'hook'			=> "edit_entries_additional_celldata",
				'settings'		=> '',
				'priority'		=> 10,
				'version'		=> $this->version,
				'enabled'		=> "y"
			)
		);

		// run all sql queries
		foreach ($sql as $query)
		{
			$DB->query($query);
		}
		return TRUE;
	}	
	
	// --------------------------------
	//  Disable Extension
	// -------------------------------- 
	function disable_extension()
	{
		global $DB;
		$DB->query("DELETE FROM exp_extensions WHERE class = '" . get_class($this) . "'");
	}
	
	// --------------------------------
	//  Update Extension
	// --------------------------------  
	function update_extension($current='')
	{
		global $DB;
		
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
		
		$DB->query("UPDATE exp_extensions
		            SET version = '".$DB->escape_str($this->version)."'
		            WHERE class = '".get_class($this)."'");
	}
	// END
// ============================================================================


	// --------------------------------
	//  Add Category Heading to Table
	// --------------------------------
	
	function view_counts_additional_tableheader()
	{
		global $DSP, $LANG, $EXT;
		
		$LANG->fetch_language_file('view_counts');
		$extra = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';
		return $extra.$DSP->table_qcell('tableHeadingAlt', $LANG->line('counts'));
	}
	// END


	// ---------------------------------
	//  Add Categories for Entries
	// ---------------------------------
	
	function view_counts_additional_celldata($row)
	{	
		global $DSP, $LANG, $EXT, $DB;
		global $i_view_counts;
		 
		if (empty($i_view_counts)) {
			$i_view_counts = 0;
		}
		
		$total_count = "-";

		$query = $DB->query("SELECT view_count_one, view_count_two, view_count_three, view_count_four FROM exp_weblog_titles WHERE entry_id='".$row['entry_id']."'");
		$counts = $query->row;
		
		$total_count = ($counts['view_count_one']+$counts['view_count_two']+$counts['view_count_three']+$counts['view_count_four']);
		$view_counts = '<div class="smallNoWrap">'.$total_count.'</div>';

		$style = ($i_view_counts % 2) ? 'tableCellOne' : 'tableCellTwo'; $i_view_counts++;
		$extra = ($EXT->last_call !== FALSE) ? $EXT->last_call : '';
		return $extra.$DSP->table_qcell($style, $view_counts);
		
	}

/* END class */
}
/* End of file ext.view_counts.php */
/* Location: ./system/extensions/ext.view_counts.php */ 