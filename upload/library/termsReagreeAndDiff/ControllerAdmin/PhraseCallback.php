<?php
class termsReagreeAndDiff_ControllerAdmin_PhraseCallback
{
	public static function load_class($class, array &$extend)
	{
		if ($class == 'XenForo_ControllerAdmin_Phrase')
		{
			$classname = 'termsReagreeAndDiff_ControllerAdmin_Phrase';
			if(!array_key_exists($classname,$extend)){
				$extend[] = $classname;
			}
		}
	}
}
