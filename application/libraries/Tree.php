<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Tree Class
 * 
 * This class defines an implement of a tree structure
 * - Notice: this class uses a  get_instance() function to generate a
 * -- copy of the object, in order to avoid error reports by
 * -- CodeIgniter
 * 
 * @author		ZhaoSusen
 * @since		Version 1.0.0
 */
class Tree {
	public $id;
	public $value;
	public $level;
	public $childs;

	function get_instance($id, $value, $childs, $level = -1) {
		$this->id = $id;
		$this->value = $value;
		$this->childs = $childs;
		$this->level = $level;
		return (clone $this);
	}
}
