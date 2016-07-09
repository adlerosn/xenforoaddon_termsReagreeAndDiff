<?php
class termsReagreeAndDiff_ControllerPublic_HelpCallback
{
	public static function load_class($class, array &$extend)
	{
		if ($class == 'XenForo_ControllerPublic_Help')
		{
			$classname = 'termsReagreeAndDiff_ControllerPublic_Help';
			if(!array_key_exists($classname,$extend)){
				$extend[] = $classname;
			}
		}
	}
}
