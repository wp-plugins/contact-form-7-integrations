<?php
/*
 * Abstract class from which all others get common properties and routines.
 * THe idea is to program to an interface and not to an implementation, leave it as loose as posible.=
 * Company: contactUs.com
 * */
 
 abstract class CF7_cloud_interface{
	 
	 /*
	  * the idea here is create the common method, get list of values and get specific value from any table
	  * and all other classes extend this one
	  * */
	 protected function get_values(){}
	 
	 
	 protected function get_value(){}
	 
	 /*
	  * TODO: check which others can be included
	  * */
	 

}

/* end of abstract interface - cbc_interface.php */
