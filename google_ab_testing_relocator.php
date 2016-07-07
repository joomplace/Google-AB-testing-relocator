<?php
/**
* JoomlaQuiz system plugin for Joomla
* @version $Id: jq_alphauserpoints.php 2009-11-16 17:30:15
* @package JoomlaQuiz
* @subpackage jq_alphauserpoints.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemGoogle_ab_testing_relocator extends JPlugin
{ 
	/*
	 * Constructor
	 */
	function plgSystemGoogle_ab_testing_relocator(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	public function onAfterRender()
	{
		$app = JFactory::getApplication();

		if($app->isSite()){
			$buffer = $app->getBody();
			$pattern = '((function\sutmx.*?)(utmx\(\'url\',\'A\/B\'\);))';
			preg_match_all('/'.$pattern.'/s', $buffer, $matches, PREG_PATTERN_ORDER);
			/* GA AB testing script*/
			$script = '<script>'.$matches[2][0].'</script><script>'.$matches[3][0].'</script>';
			//preg_replace('/'.$pattern.'/s', '', $buffer);
			$buffer = str_replace(array($matches[0][0],'<head>'),array('',"<head>\r\n".$script), $buffer);
			$this->checkBuffer($buffer);

			$app->setBody($buffer);
		}

		return true;
	}

	/**
	 * Check the buffer.
	 *
	 * @param   string  $buffer  Buffer to be checked.
	 *
	 * @return  void
	 */
	private function checkBuffer($buffer)
	{
		if ($buffer === null)
		{
			switch (preg_last_error())
			{
				case PREG_BACKTRACK_LIMIT_ERROR:
					$message = "PHP regular expression limit reached (pcre.backtrack_limit)";
					break;
				case PREG_RECURSION_LIMIT_ERROR:
					$message = "PHP regular expression limit reached (pcre.recursion_limit)";
					break;
				case PREG_BAD_UTF8_ERROR:
					$message = "Bad UTF8 passed to PCRE function";
					break;
				default:
					$message = "Unknown PCRE error calling PCRE function";
			}

			throw new RuntimeException($message);
		}
	}
}