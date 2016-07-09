<?php
class termsReagreeAndDiff_ControllerPublic_RegisterCallback
{
	public static function load_class($class, array &$extend)
	{
		if ($class == 'XenForo_ControllerPublic_Register')
		{
			$classname = 'termsReagreeAndDiff_ControllerPublic_Register';
			if(!array_key_exists($classname,$extend)){
				$extend[] = $classname;
			}
		}
	}
}
