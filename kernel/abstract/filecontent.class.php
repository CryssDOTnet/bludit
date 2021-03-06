<?php defined('BLUDIT') or die('Bludit CMS.');

class fileContent
{
	public $vars;

	function __construct($path)
	{
		if($this->build($path)===false) {
			$this->vars = false;
		}
	}

	// Return true if valid
	public function isValid()
	{
		return($this->vars!==false);
	}

	public function getField($field)
	{
		if(isset($this->vars[$field])) {
			return $this->vars[$field];
		}

		return false;
	}

	// $notoverwrite true if you don't want to replace the value if are set previusly
	public function setField($field, $value, $overwrite=true)
	{
		if($overwrite || empty($this->vars[$field])) {
			$this->vars[$field] = $value;
		}

		return true;
	}

	private function build($path)
	{
		if( !Sanitize::pathFile($path, 'index.txt') ) {
			return false;
		}

		$tmp = 0;
		$lines = file($path.'index.txt');
		foreach($lines as $lineNumber=>$line)
		{
			$parts = array_map('trim', explode(':', $line, 2));

			// Lowercase variable
			$parts[0] = Text::lowercase($parts[0]);

			// If variables is content then break the foreach and process the content after.
			if($parts[0]==='content')
			{
				$tmp = $lineNumber;
				break;
			}

			if( !empty($parts[0]) && !empty($parts[1]) ) {
				// Sanitize all fields, except Content.
				$this->vars[$parts[0]] = Sanitize::html($parts[1]);
			}
		}

		// Process the content.
		if($tmp!==0)
		{
			// Next line after "Content:" variable
			$tmp++;

			// Remove lines after Content
			$output = array_slice($lines, $tmp);

			if(!empty($parts[1])) {
				array_unshift($output, "\n");
				array_unshift($output, $parts[1]);
			}

			$implode = implode($output);
			$this->vars['content'] = $implode;

			// Sanitize content.
			//$this->vars['content'] = Sanitize::html($implode);
		}

	}

}
